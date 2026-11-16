<?php

declare(strict_types=1);

namespace backend\tests\Functional;

use backend\tests\Support\FunctionalTester;
use common\fixtures\UserFixture;
use common\models\User;
use common\models\UserDetails;
use Yii;
use yii\helpers\Url;

final class UserCest
{
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'admin_data.php',
            ],
        ];
    }

    public function _before(FunctionalTester $I): void
    {
        $admin = $I->grabFixture('user', 'admin');
        $auth = Yii::$app->authManager;
        $auth->assign($auth->getRole('sysAdmin'), $admin->id);
        $I->amLoggedInAs($admin);
    }

    public function createUser(FunctionalTester $I): void
    {
        $I->amOnRoute('user/create');
        $I->submitForm('#form-user', [
            'UserForm[name]' => 'Ana Silva',
            'UserForm[username]' => 'ana',
            'UserForm[email]' => 'ana@example.com',
            'UserForm[password]' => 'password_123',
            'UserForm[role]' => 'voluntario',
            'UserForm[status]' => User::STATUS_ACTIVE,
        ]);

        $I->seeRecord(User::class, [
            'username' => 'ana',
            'email' => 'ana@example.com',
            'status' => User::STATUS_ACTIVE,
            'deleted_at' => null,
        ]);
        $I->seeRecord(UserDetails::class, [
            'name' => 'Ana Silva',
            'account_type' => UserDetails::TYPE_VOLUNTEER,
        ]);

        $user = User::findOne(['username' => 'ana']);
        $I->assertNotNull(Yii::$app->authManager->getAssignment('voluntario', $user->id));
    }

    public function updateUserWithoutPassword(FunctionalTester $I): void
    {
        $user = new User(['username' => 'edita', 'email' => 'edita@example.com']);
        $user->setPassword('password_123');
        $user->generateAuthKey();
        $user->save(false);
        $hash = $user->password_hash;

        $I->amOnRoute('user/update', ['id' => $user->id]);
        $I->dontSeeElement('#form-user .is-invalid');
        $I->submitForm('#form-user', [
            'UserForm[name]' => 'Nome Novo',
            'UserForm[username]' => 'edita',
            'UserForm[email]' => 'edita@example.com',
            'UserForm[password]' => '',
            'UserForm[role]' => 'empresa',
            'UserForm[status]' => User::STATUS_ACTIVE,
        ]);

        $I->seeRecord(UserDetails::class, ['user_id' => $user->id, 'name' => 'Nome Novo']);
        $user->refresh();
        $I->assertSame($hash, $user->password_hash);
        $I->assertNotNull(Yii::$app->authManager->getAssignment('empresa', $user->id));
    }

    public function cannotChangeOwnRoleOrStatus(FunctionalTester $I): void
    {
        $admin = $I->grabFixture('user', 'admin');

        $I->amOnRoute('user/update', ['id' => $admin->id]);
        $I->submitForm('#form-user', [
            'UserForm[name]' => 'Admin',
            'UserForm[username]' => 'admin',
            'UserForm[email]' => 'admin@example.com',
            'UserForm[password]' => '',
            'UserForm[role]' => 'voluntario',
            'UserForm[status]' => User::STATUS_INACTIVE,
        ]);

        $I->see('Não podes alterar o teu próprio perfil ou estado.');
        $I->assertNotNull(Yii::$app->authManager->getAssignment('sysAdmin', $admin->id));
        $I->seeRecord(User::class, ['id' => $admin->id, 'status' => User::STATUS_ACTIVE]);
    }

    public function deleteUserIsSoft(FunctionalTester $I): void
    {
        $user = new User(['username' => 'rui', 'email' => 'rui@example.com']);
        $user->setPassword('password_123');
        $user->generateAuthKey();
        $user->save(false);

        $I->sendAjaxPostRequest(Url::to(['/user/delete', 'id' => $user->id]));

        $user->refresh();
        $I->assertNotNull($user->deleted_at);
        $I->assertEqualsWithDelta(time(), $user->deleted_at, 5);
    }
}

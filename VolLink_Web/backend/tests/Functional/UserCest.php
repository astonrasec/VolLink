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

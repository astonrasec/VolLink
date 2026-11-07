<?php

declare(strict_types=1);

namespace common\tests\Unit\Models;

use Codeception\Test\Unit;
use common\fixtures\UserFixture;
use common\models\LoginForm;
use common\models\User;
use common\models\UserDetails;
use common\tests\Support\UnitTester;
use Yii;

final class LoginFormTest extends Unit
{
    protected UnitTester $tester;
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function testLoginNoUser(): void
    {
        $model = new LoginForm(
            [
                'username' => 'not_existing_username',
                'password' => 'not_existing_password',
            ],
        );

        verify($model->login())
            ->false();
        verify(Yii::$app->user->isGuest)
            ->true();
    }

    public function testLoginWrongPassword(): void
    {
        $model = new LoginForm(
            [
                'username' => 'bayer.hudson',
                'password' => 'wrong_password',
            ],
        );

        verify($model->login())
            ->false();
        verify($model->errors)
            ->arrayHasKey('password');
        verify(Yii::$app->user->isGuest)
            ->true();
    }

    public function testLoginCorrect(): void
    {
        $model = new LoginForm(
            [
                'username' => 'bayer.hudson',
                'password' => 'password_0',
            ],
        );

        verify($model->login())
            ->true();
        verify($model->errors)
            ->arrayHasNotKey('password');
        verify(Yii::$app->user->isGuest)
            ->false();
    }

    public function testPendingCompanyCannotLogin(): void
    {
        UserDetails::deleteAll();

        $user = new User([
            'username' => 'empresa@example.com',
            'email' => 'empresa@example.com',
            'status' => User::STATUS_ACTIVE,
        ]);
        $user->setPassword('Test1234');
        $user->generateAuthKey();
        verify($user->save(false))->true();

        $details = new UserDetails([
            'user_id' => $user->id,
            'name' => 'Empresa Teste',
            'account_type' => UserDetails::TYPE_COMPANY,
            'account_status' => UserDetails::STATUS_PENDING,
            'email_verified' => true,
        ]);
        verify($details->save(false))->true();

        $model = new LoginForm([
            'username' => 'empresa@example.com',
            'password' => 'Test1234',
        ]);

        verify($model->login())->false();
        verify(Yii::$app->user->isGuest)->true();
    }
}

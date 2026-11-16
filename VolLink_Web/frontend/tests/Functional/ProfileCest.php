<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\fixtures\UserFixture;
use common\models\User;
use common\models\UserDetails;
use frontend\tests\Support\FunctionalTester;

final class ProfileCest
{
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'profile_user.php',
            ],
        ];
    }

    public function _before(FunctionalTester $I): void
    {
        UserDetails::deleteAll();

        $user = $I->grabFixture('user', 'volunteer');
        $I->haveRecord(UserDetails::class, [
            'user_id' => $user->id,
            'name' => 'Maria Teste',
            'account_type' => UserDetails::TYPE_VOLUNTEER,
            'account_status' => UserDetails::STATUS_APPROVED,
            'skills' => 'Cozinha',
        ]);
    }

    public function guestIsSentToLogin(FunctionalTester $I): void
    {
        $I->amOnRoute('profile/index');
        $I->seeInCurrentUrl('login');
    }

    public function seeOwnProfile(FunctionalTester $I): void
    {
        $I->amLoggedInAs($I->grabFixture('user', 'volunteer'));
        $I->amOnRoute('profile/index');

        $I->see('Maria Teste', 'h2');
        $I->see('maria@example.com');
        $I->see('Cozinha');
        $I->dontSee('Ana Silva');
    }

    public function editProfile(FunctionalTester $I): void
    {
        $I->amLoggedInAs($I->grabFixture('user', 'volunteer'));
        $I->amOnRoute('profile/update');
        $I->submitForm('#form-profile', [
            'ProfileForm[name]' => 'Maria Silva',
            'ProfileForm[skills]' => 'Primeiros socorros',
            'ProfileForm[birthDate]' => '1998-04-12',
        ]);

        $I->seeRecord(UserDetails::class, [
            'name' => 'Maria Silva',
            'skills' => 'Primeiros socorros',
            'birth_date' => '1998-04-12',
        ]);
        $I->see('Maria Silva', 'h2');
    }

    public function editProfileRequiresName(FunctionalTester $I): void
    {
        $I->amLoggedInAs($I->grabFixture('user', 'volunteer'));
        $I->amOnRoute('profile/update');
        $I->submitForm('#form-profile', ['ProfileForm[name]' => '']);

        $I->seeValidationError('Nome cannot be blank.');
        $I->seeRecord(UserDetails::class, ['name' => 'Maria Teste']);
    }

    public function changePasswordWithWrongCurrent(FunctionalTester $I): void
    {
        $I->amLoggedInAs($I->grabFixture('user', 'volunteer'));
        $I->amOnRoute('profile/index');
        $I->submitForm('#form-password', [
            'ChangePasswordForm[current]' => 'errada',
            'ChangePasswordForm[new]' => 'nova_password_1',
            'ChangePasswordForm[repeat]' => 'nova_password_1',
        ]);

        $I->see('A palavra-passe atual está errada.');
    }

    public function changePassword(FunctionalTester $I): void
    {
        $user = $I->grabFixture('user', 'volunteer');
        $I->amLoggedInAs($user);
        $I->amOnRoute('profile/index');
        $I->submitForm('#form-password', [
            'ChangePasswordForm[current]' => 'Test1234',
            'ChangePasswordForm[new]' => 'nova_password_1',
            'ChangePasswordForm[repeat]' => 'nova_password_1',
        ]);

        $I->see('Palavra-passe alterada.');
        $I->assertTrue(User::findOne($user->id)->validatePassword('nova_password_1'));
    }
}

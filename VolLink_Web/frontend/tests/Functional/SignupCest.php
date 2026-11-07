<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\models\User;
use common\models\UserDetails;
use frontend\tests\Support\FunctionalTester;

final class SignupCest
{
    protected string $formId = '#form-signup';
    public function _before(FunctionalTester $I): void
    {
        $I->amOnRoute('site/signup');
    }

    public function signupWithEmptyFields(FunctionalTester $I): void
    {
        $I->see('Criar conta', 'h2');
        $I->see('Escolhe como queres usar a VolLink.');
        $I->submitForm($this->formId, []);
        $I->seeValidationError('Username cannot be blank.');
        $I->seeValidationError('Email cannot be blank.');
        $I->seeValidationError('Password cannot be blank.');
    }

    public function signupWithWrongEmail(FunctionalTester $I): void
    {
        $I->submitForm(
            $this->formId,
            [
                'SignupForm[username]' => 'tester',
                'SignupForm[email]' => 'ttttt',
                'SignupForm[password]' => 'tester_password',
            ]
        );
        $I->dontSee('Username cannot be blank.', '.invalid-feedback');
        $I->dontSee('Password cannot be blank.', '.invalid-feedback');
        $I->see('Email is not a valid email address.', '.invalid-feedback');
    }

    public function signupSuccessfully(FunctionalTester $I): void
    {
        $I->submitForm(
            $this->formId,
            [
                'SignupForm[username]' => 'tester',
                'SignupForm[email]' => 'tester.email@example.com',
                'SignupForm[password]' => 'tester_password',
            ],
        );

        $I->seeRecord(
            User::class,
            [
                'username' => 'tester',
                'email' => 'tester.email@example.com',
                'status' => User::STATUS_INACTIVE,
            ],
        );

        $I->seeEmailIsSent();
        $I->see('Thank you for registration. Please check your inbox for verification email.');
    }

    public function signupCompanySuccessfully(FunctionalTester $I): void
    {
        $I->submitForm(
            $this->formId,
            [
                'SignupForm[accountType]' => 'company',
                'SignupForm[companyName]' => 'Associação Horizonte',
                'SignupForm[email]' => 'empresa@example.com',
                'SignupForm[password]' => 'tester_password',
            ],
        );

        $I->seeRecord(
            User::class,
            [
                'username' => 'empresa@example.com',
                'email' => 'empresa@example.com',
                'status' => User::STATUS_INACTIVE,
            ],
        );
        $I->seeRecord(
            UserDetails::class,
            [
                'name' => 'Associação Horizonte',
                'account_type' => UserDetails::TYPE_COMPANY,
                'account_status' => UserDetails::STATUS_PENDING,
            ],
        );

        $I->seeEmailIsSent();
        $I->see('Conta de empresa criada. Confirma o email e aguarda a validação do SysAdmin.');
    }
}

<?php

declare(strict_types=1);

namespace backend\tests\Functional;

use backend\tests\Support\FunctionalTester;
use common\fixtures\UserFixture;

final class LoginCest
{
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'login_data.php',
            ],
        ];
    }

    public function loginUser(FunctionalTester $I): void
    {
        $I->amOnRoute('/site/login');
        $I->fillField('LoginForm[username]', 'erau');
        $I->fillField('LoginForm[password]', 'password_0');
        $I->click('login-button');

        // O utilizador de teste não tem a permissão accessBackoffice: a sessão inicia-se,
        // mas o dashboard é-lhe negado. Basta confirmar que saiu do formulário de login.
        $I->dontSeeElement('#login-form');
        $I->seeLink('Sair');
    }
}

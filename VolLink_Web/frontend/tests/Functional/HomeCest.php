<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use frontend\tests\Support\FunctionalTester;

final class HomeCest
{
    public function checkOpen(FunctionalTester $I): void
    {
        $I->amOnRoute('site/index');
        $I->see('Encontra a tua próxima causa', 'h1');
        $I->seeLink('Entrar');
        $I->click('Criar conta');
        $I->see('Criar conta', 'h2');
    }
}

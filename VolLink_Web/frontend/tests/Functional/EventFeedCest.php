<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\fixtures\EventFixture;
use common\fixtures\ShiftFixture;
use common\fixtures\UserDetailsFixture;
use common\fixtures\UserFixture;
use frontend\tests\Support\FunctionalTester;

final class EventFeedCest
{
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
            'userDetails' => [
                'class' => UserDetailsFixture::class,
                'dataFile' => codecept_data_dir() . 'event_feed_user_details.php',
            ],
            'event' => [
                'class' => EventFixture::class,
                'dataFile' => codecept_data_dir() . 'event_feed_events.php',
            ],
            'shift' => [
                'class' => ShiftFixture::class,
                'dataFile' => codecept_data_dir() . 'event_feed_shifts.php',
            ],
        ];
    }

    public function seePublishedEventsWithoutAuthentication(FunctionalTester $I): void
    {
        $I->amOnRoute('site/index');

        $I->see('Encontra a tua próxima causa', 'h1');
        $I->see('Limpeza da praia');
        $I->see('Banco alimentar');
        $I->dontSee('Rascunho secreto');
        $I->see('2 eventos');
    }

    public function filterByCategory(FunctionalTester $I): void
    {
        $I->amOnRoute('site/index', ['category' => 'Social']);

        $I->see('Banco alimentar');
        $I->dontSee('Limpeza da praia');
    }

    public function filterByLocation(FunctionalTester $I): void
    {
        $I->amOnRoute('site/index', ['location' => 'Porto']);

        $I->see('Banco alimentar');
        $I->dontSee('Limpeza da praia');
    }

    public function filterByDateRange(FunctionalTester $I): void
    {
        $I->amOnRoute('site/index', ['date_from' => '2026-12-01', 'date_to' => '2026-12-31']);

        $I->see('Banco alimentar');
        $I->dontSee('Limpeza da praia');
    }

    public function searchByText(FunctionalTester $I): void
    {
        $I->amOnRoute('site/index', ['q' => 'praia']);

        $I->see('Limpeza da praia');
        $I->dontSee('Banco alimentar');
    }

    public function openEventDetail(FunctionalTester $I): void
    {
        $I->amOnRoute('event/view', ['id' => 1]);

        $I->see('Limpeza da praia', 'h1');
        $I->see('Uma atividade aberta à comunidade');
        $I->see('Turno da manhã');
        $I->see('Turno da tarde');
        $I->see('Trabalho em equipa');
        $I->see('Associação Horizonte');
        $I->see('Verificada');
        $I->see('20 de 20 vagas');
    }

    public function draftEventIsNotPublic(FunctionalTester $I): void
    {
        $I->amOnRoute('event/view', ['id' => 3]);

        $I->seeResponseCodeIs(404);
    }
}

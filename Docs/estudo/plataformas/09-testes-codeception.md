# Plataformas 09 — Testes com Codeception (guia complementar)

> ⚠ **Não há material de aulas sobre Codeception nas pastas** (`Docs/info/plataformas/` não inclui nenhum PDF de testes). O que existe é: o **enunciado** (testes mínimos obrigatórios, 10 % da nota) e o **programa** da UC (Integração Contínua: white-box vs black-box, especificação e realização de testes).
> Este documento é **complementar (nosso)**. Confirmar com os docentes se foram dados materiais próprios.

---

## 1. O que o enunciado exige (resumo)

| Tipo | Quantidade | O que verificar |
|---|---:|---|
| **Unitário** | **3 casos** sobre **um modelo de domínio** | (1) campos obrigatórios · (2) validação estrutural (formato/intervalos/tipos) · (3) persistência ActiveRecord **ou** lógica de negócio interna |
| **Funcional** | **1 caso** | Submissão do formulário **Create** da **entidade principal** e **estado final persistido** na BD |
Framework: **Codeception** (peso **10 %**).

## 2. Conceitos do programa
- **White-box:** testa-se conhecendo o código (ex.: testes unitários de um modelo, caminhos de `rules()`).
- **Black-box:** testa-se pelo comportamento externo, sem olhar à implementação (ex.: teste funcional de um formulário).
- **Integração contínua:** correr os testes a cada alteração para reduzir risco (o projeto usa PRs + CI; ver `Docs/src/desenvolvimento.md`).

## 3. O que o repositório já tem (`VolLink_Web/`)
- `codeception.yml` global (inclui `common`, `frontend`, `backend`); dependências `codeception/codeception ^5`, `module-yii2`, `module-asserts`, `verify`, etc.
- Suites em `common/tests/Unit`, `frontend/tests/{Unit,Functional,Acceptance}`, `backend/tests/{Unit,Functional}`.
- Existe `common/tests/Unit/Models/` (modelos testados em *unit*).

## 4. Comandos
```bash
# preparar (BD de testes + actors gerados)
php yii_test migrate              # migra a BD de testes (yii_test usa a config de teste)
vendor/bin/codecept build

# correr tudo / por suite / por ficheiro
vendor/bin/codecept run
vendor/bin/codecept run -c common unit
vendor/bin/codecept run -c backend functional
vendor/bin/codecept run -c common unit Models/EventTest.php --debug
```

## 5. Teste unitário de um modelo (`common/tests/Unit/Models/EventTest.php`) — esboço nosso
```php
<?php
namespace common\tests\Unit\Models;

use common\models\Event;
use Codeception\Test\Unit;

class EventTest extends Unit
{
    // (1) campos obrigatórios
    public function testRequiredFields(): void
    {
        $event = new Event();
        $this->assertFalse($event->validate());
        $this->assertArrayHasKey('title', $event->errors);
        $this->assertArrayHasKey('description', $event->errors);
    }

    // (2) validação estrutural (tamanho/intervalo/tipo)
    public function testValidationRules(): void
    {
        $event = new Event(['title' => str_repeat('a', 151), 'capacity' => -1]);
        $event->validate();
        $this->assertArrayHasKey('title', $event->errors);      // max 150
        $this->assertArrayHasKey('capacity', $event->errors);   // inteiro >= 1
    }

    // (3) persistência ActiveRecord
    public function testSaveAndFind(): void
    {
        $event = new Event([ /* atributos válidos, creator_id de um user de fixture */ ]);
        $this->assertTrue($event->save());
        $this->assertNotNull(Event::findOne($event->id));
        $this->assertSame(Event::STATUS_DRAFT, $event->status);   // default
    }
}
```
> O nome dos atributos/regras tem de coincidir com `common/models/Event.php` real. Usar **fixtures** (`common/fixtures`) para ter um `user` empresa válido (FK `creator_id`).

## 6. Teste funcional do formulário Create — esboço nosso
```php
<?php
namespace frontend\tests\Functional;

use frontend\tests\FunctionalTester;
use common\models\Event;

class EventCreateCest
{
    public function _before(FunctionalTester $I): void
    {
        $I->amLoggedInAs(/* user empresa de fixture */ 1);
    }

    public function createEventWithValidData(FunctionalTester $I): void
    {
        $I->amOnRoute('event/create');
        $I->submitForm('#event-form', [
            'Event[title]'       => 'Limpeza da praia',
            'Event[description]' => 'Ação de voluntariado',
            // ... restantes campos obrigatórios
        ]);
        $I->seeRecord(Event::class, ['title' => 'Limpeza da praia']);   // estado persistido
        $I->see('Limpeza da praia');
    }

    public function cannotCreateWithoutTitle(FunctionalTester $I): void
    {
        $I->amOnRoute('event/create');
        $I->submitForm('#event-form', ['Event[title]' => '']);
        $I->see('cannot be blank');   // ou mensagem PT configurada
        $I->dontSeeRecord(Event::class, ['description' => '...']);
    }
}
```
- `Yii2` module (`config` da suite) fornece `amOnRoute`, `amLoggedInAs`, `seeRecord`, `dontSeeRecord`, `grabRecord`, `haveRecord`.
- Selecionar **a entidade principal do VolLink**: `Event` (criado pela empresa) é a candidata natural; garantir que o utilizador do teste tem a permissão `eventCreate`.

## 7. Checklist de entrega (10 %)
- [ ] 3 testes **unitários** sobre o **mesmo** modelo (obrigatórios / validação / persistência ou lógica).
- [ ] 1 teste **funcional** do Create da entidade principal que **verifica o registo na BD**.
- [ ] Testes passam com `vendor/bin/codecept run` num ambiente limpo (BD de teste migrada).
- [ ] Mencionado no **README** como correr os testes.

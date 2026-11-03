# Desenvolvimento

## Git

A `main` só recebe merges. **Todo o trabalho nasce numa branch.**

### Nomenclatura

| Prefixo | Uso | Exemplo |
|---|---|---|
| `feat/` | Funcionalidade nova | `feat/models` |
| `fix/` | Correção de erro | `fix/login-email` |
| `chore/` | Configuração e ferramentas | `chore/pretty-urls` |
| `docs/` | Documentação | `docs/readme-and-docs-site` |
| `refactor/` | Reestruturação sem alterar comportamento | `refactor/event-rules` |

### Ciclo

```powershell
# 1. partir da main atualizada
git switch main
git pull --ff-only

# 2. criar a branch da tarefa
git switch -c feat/models

# 3. trabalhar com commits pequenos
git add common/models
git commit -m "feat(models): add event and shift models"

# 4. publicar e abrir PR
git push -u origin feat/models
```

No GitHub, o PR é merged com **Rebase and merge** e a branch é apagada. Depois:

```powershell
git switch main
git pull --ff-only
git branch -d feat/models
```

### Commits

Formato: **Conventional Commits** — `tipo(âmbito): descrição no imperativo`.

```text
feat(auth): add email verification on signup
fix(rbac): allow company to manage own events
docs(setup): document apache aliases
chore(deps): track composer.lock
```

- Mensagem em inglês, curta e no imperativo.
- Um commit = uma alteração coerente.
- Sem `Co-authored-by`, sem créditos automáticos.

## Convenções de código

| Regra | Detalhe |
|---|---|
| PHP | 8.2+, `declare(strict_types=1);` em ficheiros novos |
| Identificadores | Inglês, `camelCase` para métodos, `snake_case` para colunas |
| Textos da UI | Português de Portugal |
| Estilo | PSR-12 (`composer cs`) |
| SQL | Sempre por ActiveRecord ou Query Builder, nunca concatenado |
| Estados | Constantes PHP no modelo, nunca strings soltas no código |

## Templates de interface

| Aplicação | Template permitido |
|---|---|
| `frontend/` | Template do [htmlcodex.com](https://htmlcodex.com/) ou HTML e CSS |
| `backend/` | [`hail812/yii2-adminlte3`](https://github.com/hail812/yii2-adminlte3) (AdminLTE 3) |

- Não instalar nem copiar para o repositório outros templates, temas ou *dashboards*.
- Quem usar um template do HTML Codex mantém o crédito do autor no rodapé (licença CC BY 4.0).
- Detalhes em [Arquitetura](arquitetura.md#templates-de-interface).

## Migrações

```powershell
cd VolLink_Web
php yii migrate/create add_shift_capacity
php yii migrate
php yii migrate/down 1
```

Regras:

1. Nomes `mYYMMDD_HHMMSS_descricao.php` (gerados pelo Yii).
2. Sempre com `safeUp()` e `safeDown()`.
3. Estados em `VARCHAR`, **nunca** `ENUM` do MySQL.
4. Chaves estrangeiras com `ON DELETE` explícito.
5. Testar `up` e `down` antes de abrir o PR.

## RBAC

Ao acrescentar uma permissão:

1. Adicionar em `console/controllers/RbacController.php` (`PERMISSIONS`).
2. Ligar aos roles em `LINKS`.
3. Correr `php yii rbac/init`.
4. Confirmar com `php yii rbac/list`.

## Como adicionar uma funcionalidade

Para manter o sistema simples e previsível, uma funcionalidade nova segue sempre esta ordem:

1. **Requisito e user story** — acrescentar o RF em [Requisitos](requisitos.md) e a US em [User Stories](user-stories.md). Se não couber no âmbito, vai para o [Roadmap](roadmap.md).
2. **Migração** — só se houver dados novos. Criar `mYYMMDD_HHMMSS_descricao.php` com `safeUp()`/`safeDown()`.
3. **Modelo** — ActiveRecord em `common/models`, com constantes de estado e relações.
4. **Permissão** — se a funcionalidade for restrita, acrescentar em `RbacController` (`PERMISSIONS` e `LINKS`) e correr `php yii rbac/init`.
5. **API** — se a app Android precisar, acrescentar o endpoint em `api/controllers` e documentá-lo em [Requisitos](requisitos.md#endpoints-da-api).
6. **Web** — controlador e vista em `frontend/` ou `backend/`, com `AccessControl`/`Yii::$app->user->can()` e o [template permitido](#templates-de-interface).
7. **Android** — ecrã, chamada Volley e cache SQLite, se aplicável.
8. **Testes** — unitário para a regra de negócio e funcional para o fluxo principal.
9. **Documentação** — atualizar a página de funcionalidades e o roadmap.

!!! tip "Regra de ouro"
    Se a funcionalidade exigir uma **tabela nova**, pergunta primeiro se é mesmo do MVP. O núcleo atual tem 7 tabelas de domínio; qualquer adição tem de ser justificada.

## Qualidade

```powershell
composer cs          # phpcs (PSR-12)
composer cs-fix      # corrigir automaticamente
composer static      # phpstan
composer tests       # codeception
```

## Estrutura de um controlador

```php
declare(strict_types=1);

namespace frontend\controllers;

use common\models\Event;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

final class EventController extends Controller
{
    public function actionView(int $id): string
    {
        $event = Event::findOne($id);

        if ($event === null) {
            throw new NotFoundHttpException('Evento não encontrado.');
        }

        return $this->render('view', ['event' => $event]);
    }
}
```

As verificações de acesso ficam em `behaviors()` com `AccessControl`, ou em `Yii::$app->user->can(...)` quando dependem do evento.

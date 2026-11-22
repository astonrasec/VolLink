# Plataformas 08 — Aplicação ao VolLink (checklist de implementação)

> Cruza o **enunciado de PLSI** e a matéria das aulas com o que **já está documentado/implementado** no repositório (`Docs/src/*.md` e `VolLink_Web/`).
> Estado considerado (branch `feat/events-public`): modelos ActiveRecord criados, US10 (registo de empresa) e US1 web (feed público) feitos; `feat/users-crud`, `feat/profile-and-signup` e `feat/web-templates` existem no remoto.

---

## 1. Arquitetura do repositório vs. matéria

| Matéria (aula) | Como está no VolLink |
|---|---|
| Template **Advanced** ([`04`](04-basic-advanced-master-detail.md)) | `VolLink_Web/` com `frontend/`, `backend/`, `common/`, `console/`, `environments/` (+ `api/` planeada) |
| Ficheiros `*-local` fora do Git ([`05`](05-autenticacao-acf-rbac.md)) | Credenciais só em `common/config/main-local.php` (no `.gitignore`) |
| Tabela `user` intocada + 1:1 ([`05`](05-autenticacao-acf-rbac.md)) | `user_details` com `user_id UNIQUE` (relação 1:1) |
| Migrations ([`03`](03-migrations-activeform-gii.md)) | `console/migrations/` — 11 do projeto + 4 de RBAC; `php yii migrate` |
| RBAC DbManager ([`05`](05-autenticacao-acf-rbac.md)) | `authManager = yii\rbac\DbManager` (em `common/config/main.php`); `php yii rbac/init` |
| Templates de UI ([`00`](00-enunciado-e-avaliacao.md)) | Back-office **AdminLTE 3** (`hail812/yii2-adminlte3`); front-office **HTML/CSS** (permitido: htmlcodex **ou** HTML+CSS) |
| Codeception ([`09`](09-testes-codeception.md)) | Suites já criadas em `common/`, `frontend/`, `backend/` |

> Convenções do projeto: nomes em **inglês** (`snake_case` nas colunas), UI em **português de Portugal**, **sem `ENUM`** (estados em `VARCHAR` + constantes PHP), commits em *Conventional Commits*, PR por branch.

---

## 2. RBAC do VolLink (3 roles, 12 permissões, 1 regra)

| Role | Papel |
|---|---|
| `sysAdmin` | Back-office |
| `voluntario` | Website + app Android; candidata-se; **não cria eventos** |
| `empresa` | **Única** que cria/gere eventos; não se candidata; sem app Android no MVP |

| Permissão | sysAdmin | voluntario | empresa |
|---|:--:|:--:|:--:|
| `accessBackoffice` | ✅ | | |
| `accessFrontoffice` | ✅ | ✅ | ✅ |
| `accessMobile` | | ✅ | |
| `eventCreate` | | | ✅ |
| `eventManage` (regra `eventOwner`) | | | ✅ |
| `moderateEvent` | ✅ | | |
| `applicationSubmit` | | ✅ | |
| `applicationDecide` (regra `eventOwner`) | | | ✅ |
| `presenceConfirm` (regra `eventOwner`) | | | ✅ |
| `userModerate` | ✅ | | |
| `contentModerate`, `systemManage` | reservadas (sem role) | | |

Regra `eventOwner` (`common\rbac\EventOwnerRule`): devolve `true` só se o utilizador autenticado é `event.creator_id`. Uso: `Yii::$app->user->can('eventManage', ['event' => $event])`.
**Regras de ouro:** verificar **permissões** (nunca `can('sysAdmin')`); front/back separados por `accessFrontoffice`/`accessBackoffice`; nova permissão → atualizar `RbacController` + `php yii rbac/init`.

Mapeamento para a matéria: `rbac/init` = **construção por consola** (aula 05); `EventOwnerRule` = **Rule** (aula 05); atribuição no registo = `SignupForm::signup()` com `assign` (aula 05).

---

## 3. Checklist — **Fase 1** (20 % da nota da UC; PO1)

| Requisito do enunciado | Estado / Ação | Matéria |
|---|---|---|
| Instalação/config do **RBAC** com roles e permissões do Projeto | ✅ `rbac/init` (3 roles / 12 permissões / 1 regra). Verificar `rbac/list` | [`05`](05-autenticacao-acf-rbac.md) |
| **Registo** (front-office) integrado no RBAC | ✅ US10 (empresa); voluntário: `feat/profile-and-signup` — confirmar atribuição de role no `signup()` | [`05`](05-autenticacao-acf-rbac.md) §6.3 |
| **Gestão de utilizadores** (back-office) — **CRUD completo com Soft Delete** | ⏳ `feat/users-crud` — `User::delete()` → `status = 0`; listagens a excluir `status = 0`; `UserSearch` | [`06`](06-widgets-dataprovider-softdelete.md), [`07`](07-gridview-avancado-roles-por-user.md) |
| **Back-office protegido** contra utilizadores só de front-office | ACF no `backend` com `accessBackoffice` (em `behaviors()` do controller base ou do módulo) | [`05`](05-autenticacao-acf-rbac.md) §5 |
| **Template** front-office (home + menus, sem funcionalidades) | ✅ HTML/CSS próprio | — |
| **Template** back-office (dashboard + menus, sem funcionalidades) | ✅ AdminLTE 3 | — |

### 3.1 Soft delete no `User` — detalhes específicos do VolLink
- A BD documentada usa `user.status`: **10 ativo, 9 inativo, 0 eliminado**; o `UserDetails` guarda `account_status` (`PENDING/APPROVED/REJECTED/SUSPENDED`).
- O **login tem de validar `user.status` e `user_details.account_status`** (uma empresa `PENDING` não pode entrar só com `status = 10`).
- Cuidado (aula 06): definir o que acontece às **relações** de um utilizador eliminado (a FK `event.creator_id` é `RESTRICT`: não se apaga fisicamente quem criou eventos — o soft delete resolve isto) e **quem pode ver/restaurar** eliminados (permissão `userModerate`).
```php
// back-office: UserSearch::search() — listar sem eliminados
$query = User::find()->where(['!=', 'user.status', User::STATUS_DELETED]);
```

---

## 4. Checklist — **Fase 2** (80 %; PO2 + TP)

| Componente (peso) | O que cobrir |
|---|---|
| **Front-end 20 %** | Todas as páginas dos mockups (front e back) ligadas; mensagens PT; widgets (`GridView`, `ListView`, `DetailView`, `ActiveForm`) |
| **Back-end 45 %** | Requisitos funcionais RF-FO-01…20 e RF-BO-01…06 (ver `Docs/src/requisitos.md`): eventos/turnos, candidaturas, presenças/horas, perfil, dashboard, validação de empresas, suspensão, estatísticas |
| **Autenticação/Autorização 15 %** | ACF em **todos** os controllers + `can()` nas vistas/actions; `visibleButtons` com `can()`; regra `eventOwner`; sem acesso por URL direto |
| **Testes 10 %** | 3 unit (um modelo) + 1 funcional Create — [`09`](09-testes-codeception.md) |
| **Qualidade/otimização/inovação 10 %** | Eager loading (`with`/`joinWith`), `safe` nas `rules()` do Search, `Html::encode`, transações, `PHPStan`/`phpcs` (já há `phpstan.neon` e `phpcs.xml.dist`) |

### 4.1 Boas práticas de **segurança e integridade** nos CRUD do back-office (exigidas na Fase 2)
- **CSRF** ligado (ActiveForm), **delete só por POST** (`VerbFilter`: `'delete' => ['POST']`).
- **Mass assignment:** só atributos em `rules()`; `safe` apenas onde necessário; nunca `load()` de campos sensíveis (`status`, `auth_key`, `password_hash`).
- **XSS:** `Html::encode()` em saídas; **SQL injection:** só ActiveRecord/Query Builder com parâmetros.
- **Integridade:** FKs com `ON DELETE` coerente (`RESTRICT`/`CASCADE`), **transações** em operações Master+Detail (evento + turnos), validações cruzadas nos modelos (turno dentro do evento; candidatura vs turno; presença vs candidatura).
- **Passwords** com `Yii::$app->security->generatePasswordHash()` (bcrypt) — RNF-03.
- **Autorização por objeto:** permissões com regra `eventOwner` — verificar no **servidor**, não só esconder botões.

---

## 5. Mapa matéria → implementação VolLink

| Funcionalidade VolLink | Padrão da matéria |
|---|---|
| Feed de eventos com filtros e paginação (RF-FO-04/06) | `ActiveDataProvider` + `ListView`/`_event.php`; `EventSearch` com `andFilterWhere` (categoria, localização, datas) |
| Detalhe do evento com turnos (**Master-Detail**) | `Event::getShifts()` (`hasMany`), `Shift::getEvent()` (`hasOne`); **eager loading** `with('shifts')`; `actionCreate($eventId)` para turnos ([`04`](04-basic-advanced-master-detail.md) §2.3) |
| Candidaturas por turno | `Application` com `shift_id`; índice único `(user_id, shift_id)`; validação de vagas e prazo no modelo |
| Dashboard da empresa | `SqlDataProvider`/queries agregadas ([`06`](06-widgets-dataprovider-softdelete.md) §2.1) |
| Gestão de utilizadores com roles | `GridView` + coluna de cargos com `getRolesByUser` + filtro `auth_assignment.item_name` ([`07`](07-gridview-avancado-roles-por-user.md)) |
| Estados (`DRAFT`→`PUBLISHED`→…) | Constantes PHP no modelo; transições na action (verificar com `can()`) |
| Mensagens de sucesso/erro | `Yii::$app->session->setFlash()` ([`02`](02-core-componentes-routing.md) §4) |
| Pretty URLs e aliases Apache | `urlManager` ([`02`](02-core-componentes-routing.md) §2); `/vollink`, `/vollink-admin`, `/vollink-api` |
| API para o Android | App `api/` com `yii\rest\ActiveController` + `HttpBearerAuth` ([Android `10`](../android/10-api-rest-volley.md)) |

## 6. Fluxo de trabalho recomendado (por funcionalidade)
1. **Migration** (`migrate/create`) com `safeUp`/`safeDown` (chaves e índices nomeados `fk-…`, `uq-…`).
2. **Gii** → Model (+ relações) e CRUD; **adaptar** o código gerado (labels PT, colunas desnecessárias, FKs → nomes).
3. **ACF + RBAC** no controller; `visibleButtons`/`can()` nas vistas.
4. **Testes** (unit do modelo; funcional do Create).
5. `composer cs` / `phpstan`; **PR** com o commit no formato Conventional Commits.

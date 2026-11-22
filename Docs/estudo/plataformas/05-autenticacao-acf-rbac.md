# Plataformas 05 — Autenticação, Autorização, ACF e RBAC (Aula 05 + Ficha 5)

> Fontes: `Aula 05 - Yii2 Autorização, Autenticação, ACF e RBAC.pdf`, `PlatSI-Ficha-Prática-05.pdf`.
> ⚠ **Vale 15 % da nota** do projeto de Plataformas (Autenticação e Autorização) — "recorrer ao ACF, complementado pela API RBAC".
> Respostas às lacunas da ficha: **(nossas)**.

---

## 1. Autenticação vs Autorização

| Autenticação — *"Quem és?"* | Autorização — *"O que podes fazer?"* |
|---|---|
| Identificar o utilizador (login: email + password); a app confirma a identidade | Decidir se o utilizador pode executar uma ação (aceder a esta página? criar/apagar registos?) |
| | Tipos de utilizador com permissões diferentes (ex.: Cliente vs Dono da Loja) |

## 2. Tabela `user` do template Advanced

- O template **Advanced** inclui **migrations** que criam a tabela **`user`**, preparada para autenticação (suporta a identificação do utilizador via `Yii::$app`).
- ⛔ **Não mexer nesta tabela.** Para guardar informação adicional: criar **nova tabela** ligada à `user` por **FK**, relação **1:1** (ex.: `user_profile`).
```php
class User extends ActiveRecord {
    public function getUserProfile() {
        return $this->hasOne(UserProfile::class, ['id' => 'id']);   // 1:1 (slide: PK partilhada)
    }
}
// uso
$profile = Yii::$app->user->identity->userProfile;
```

## 3. Utilizador autenticado — `Yii::$app->user`

Estado do utilizador que faz os pedidos:
```php
if (Yii::$app->user->isGuest) {
    return $this->redirect(['site/login']);    // não fez login
} else {
    // autenticado → renderizar vista
}
```
(Ficha 5, 3.2) Outros exemplos:
```php
$identity = Yii::$app->user->identity;     // identidade atual; null se não autenticado
$id       = Yii::$app->user->id;           // ID atual; null se não autenticado
$isGuest  = Yii::$app->user->isGuest;

// login
$identity = User::findOne(['username' => $username]);   // (verificar password se necessário)
Yii::$app->user->login($identity);
// logout
Yii::$app->user->logout();
```
> No slide o exemplo aparece numa classe `console\controllers\ProductController` — gralha; deve ser um **controller web**.

### 3.1 `yii\web\IdentityInterface` (Ficha 5, 3 e 3.1)
O modelo `User` tem de implementar esta interface:
| Método | Função |
|---|---|
| `findIdentity($id)` | Procura a identidade pelo **ID**; usado para manter o login por **sessão** |
| `findIdentityByAccessToken($token, $type = null)` | Procura pela **access token**; usado em autenticação por token secreto (ex.: **API RESTful stateless**) |
| `getId()` | Devolve o ID do utilizador |
| `getAuthKey()` | Chave para validar o login por **cookie** (guardada no cookie e comparada com a do servidor) |
| `validateAuthKey($authKey)` | Lógica de verificação do login por cookie |

## 4. Ficheiros `*-local` no Advanced

- Os ficheiros com sufixo **`-local`** (ex.: `main-local.php`, `params-local.php`) guardam a **configuração da máquina local**.
- **Não devem ir para o repositório** → `.gitignore` com `*-local.php` / "*.local". Ficheiros comuns (`main.php`, `params.php`) **vão** para o repositório.
- *(Esta é a razão pela qual credenciais da BD ficam em `common/config/main-local.php` e não no Git — requisito também do projeto da UC.)*

## 5. Autorização simples e ACF

`isGuest` em todas as actions é demorado → **Access Control Filter (ACF)**.

- Filtro definido nos **controladores**, no método **`behaviors()`**, com regras por action baseadas em *roles*:
  - **`only`** → lista das actions a que o filtro se aplica;
  - **`roles`**: **`?`** = utilizador **anónimo** (não autenticado); **`@`** = utilizador **autenticado**.
- O Yii aplica as regras **antes** da action; sem autorização **a action nem executa**.
```php
public function behaviors() {
    return [
        'access' => [
            'class' => \yii\filters\AccessControl::class,
            'only'  => ['create', 'update', 'delete'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ],
    ];
}
```
- **Quando usar ACF:** regras simples (só autenticados veem o perfil; só anónimos acedem ao login) — evita verificações repetidas e espalhadas.
- **Limite do ACF:** casos detalhados ("um professor só gere os seus alunos", "um aluno só vê as cadeiras em que está inscrito") → precisamos de **papéis/permissões** → **RBAC**.

### 5.1 Ficha 5, ex. 4.1 — análise do ACF do `SiteController`
```php
'access' => [
    'class' => AccessControl::className(),
    'only'  => ['login', 'logout', 'signup'],
    'rules' => [
        ['allow' => true, 'actions' => ['login', 'signup'], 'roles' => ['?']],
        ['allow' => true, 'actions' => ['logout'],          'roles' => ['@']],
    ],
],
```
| Pergunta | Resposta (nossa) |
|---|---|
| a. Para que serve `only`? | Restringe o filtro às actions `login`, `logout`, `signup`; as restantes ficam sem este controlo |
| b. 1.ª regra | `allow => true` permite; `actions => ['login','signup']` aplica-se a essas actions; `roles => ['?']` só para **visitantes (não autenticados)** — quem já fez login não deve registar-se/entrar de novo |
| c. 2.ª regra | `allow => true`; `actions => ['logout']`; `roles => ['@']` só para **utilizadores autenticados** |
Quem não cumpre nenhuma regra é **negado** (anónimo → redireciona para login; autenticado → 403).

---

## 6. RBAC — Role-Based Access Control

Atribui **roles e permissões** aos utilizadores. Em vez de testar manualmente (`$user->type == 'admin'`), pergunta-se ao Yii: **`Yii::$app->user->can('editarCurso')`**.
- **Roles (papéis):** agrupam utilizadores (Admin, Professor, Aluno); um utilizador pode ter vários; um papel pode **herdar** de outros hierarquicamente inferiores.
- **Permissions:** ações granulares (`criarCurso`, `editarCurso`, `apagarCurso`, `verConteúdo`; `createPost`, `updatePost`); atribuídas **aos papéis**, não diretamente aos utilizadores.
- **Rules (regras):** classes que acrescentam lógica complexa a uma permissão (ex.: "um autor só tem `updatePost` **se o post for seu**").
- **Vantagem:** se a regra de acesso muda, altera-se a permissão/associa-se outra role, em vez de procurar todos os sítios onde estava escrita à mão.

### 6.1 Configuração — `\Yii::$app->authManager`
| | `PhpManager` | `DbManager` |
|---|---|---|
| Armazenamento | Ficheiros PHP em `@app/rbac` | **Tabelas relacionais** na BD |
| Performance | Muito rápido (árvore em memória) | Ligeiramente mais lento (queries SQL) |
| Gestão dinâmica | Rígido (alterar código/ficheiros) | **Flexível** (admin cria papéis na interface web) |
| Uso ideal | Permissões que raramente mudam | Papéis criados dinamicamente |
| Requisito | Permissões de escrita no servidor | Migração das tabelas RBAC |
```php
'components' => [
    'authManager' => [
        'class' => 'yii\rbac\PhpManager',      // ou 'yii\rbac\DbManager'
    ],
],
```
- **Onde declarar:** *Basic* → `web.php` **e** `console.php`; *Advanced* → apenas em **`common/config/main.php`**.
- **Migração das tabelas (DbManager):**
```bash
yii migrate --migrationPath=@yii/rbac/migrations
```
- **Tabelas RBAC** (não mexer; geridas pelo Yii):
| Tabela | Conteúdo |
|---|---|
| `auth_item` | Roles e permissões (`admin`, `professor`, `aluno`, `criarCurso`…) |
| `auth_item_child` | Relações role→permissão / role→role (`admin → criarCurso`) |
| `auth_assignment` | Liga **users a roles/permissões** (`user_id = 1 → admin`) |
| `auth_rule` | Regras avançadas (ex.: só edita se for o autor) |

### 6.2 Construir a hierarquia
- **Via migrations** (evolução versionada): `yii migrate/create init_rbac`.
- **Via consola** (hierarquias fixas, inicialização única): `yii rbac/init` (comando de consola nosso, que corre o código abaixo).
```php
$auth = Yii::$app->authManager;

// 1. Criar permissão
$createQuestion = $auth->createPermission('createQuestion');
$auth->add($createQuestion);

// 2. Criar role e anexar permissão
$teacher = $auth->createRole('teacher');
$auth->add($teacher);
$auth->addChild($teacher, $createQuestion);
```
### 6.3 Atribuir perfis
- **Evitar *hardcode* das atribuições** nas migrations.
- **No registo:** atribuir o perfil base após o signup com sucesso; **no backoffice:** painéis UI para gestão dinâmica.
```php
// SignupForm::signup() — após $user->save()
$auth = \Yii::$app->authManager;
$teacherRole = $auth->getRole('teacher');           // perfil existente
$auth->assign($teacherRole, $user->getId());        // associar ao ID do utilizador
```
### 6.4 Validar permissões
```php
if (\Yii::$app->user->can('createQuestion')) {
    // acesso concedido: mostrar botão, vista ou processar a ação
}
```
Também se verifica **via ACF**: `'roles' => ['createQuestion']` (ou o nome de uma role) numa regra do `AccessControl`.

### 6.5 Exemplo do slide
- Utilizador *Luis* → role `admin`; `admin` → permissões `criarCurso`, `editarCurso`…
```php
if (Yii::$app->user->can('editarCurso')) { /* pode editar */ }
```
em vez de `if ($user->type == 'admin') {...}` (no slide `=`, mas é comparação `==`).

---

## 7. Ficha 5 — Template Advanced e autenticação

**Objetivos:** instalar o **template Advanced**; analisar componentes modulares; analisar a BD gerada; configurar autenticação; **autenticação simples vs RBAC**.

1. Criar o projeto Advanced na raiz web:
   ```bash
   composer create-project --prefer-dist yiisoft/yii2-app-advanced advanced
   ```
   e seguir os passos 1–3 de <https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/start-installation.md> (`php init`, configurar `common/config/main-local.php` com a BD, `php yii migrate`).
2. **Comparação Basic vs Advanced** (✓ nos dois): estrutura do projeto, site controller, login/logout, forms, ligação à BD, comando de consola, asset bundle, testes Codeception, Twitter Bootstrap. **Só Advanced:** apps **front- e back-end**, **modelo `User` pronto a usar**, **signup e recuperação de password**.
   - 2.1 Módulos/aplicações do Advanced (resposta nossa): **frontend**, **backend**, **console** (+ pasta **common** partilhada; e **environments**/**vagrant** na raiz).
3. **Autenticação simples:** verificar se `User` implementa `yii\web\IdentityInterface` (sim, em `common/models/User.php`); analisar os 5 métodos (ver 3.1) e os exemplos de `user->identity/id/isGuest/login/logout`.
4. **Autorização básica (ACF)** — ver 5.1.
5. **RBAC** — duas partes:
   - **1.ª — dados de autorização:** definir **roles e permissões**; relações role↔permissão; **regras de acesso**; associar regras a roles/permissões; **atribuir roles a utilizadores**;
   - **2.ª — verificação:** via **ACF** ou via **código** (`\Yii::$app->user->can('createPost')`).
   - Possíveis configurações: **PhpManager** (ficheiro PHP) | **DbManager** (tabelas).
   - Seguir: <http://www.yiiframework.com/doc-2.0/guide-security-authorization.html#rbac>.
   - 5.1 Configurar o RBAC com **DbManager**.
6. **Configurar o RBAC com DbManager com os roles e permissões definidos no âmbito de Projeto em SI** → ver [`08-aplicacao-ao-vollink.md`](08-aplicacao-ao-vollink.md) (mapa de roles do VolLink).

### Esboço de `rbac/init` (nosso)
```php
// console/controllers/RbacController.php
namespace console\controllers;
use Yii;
use yii\console\Controller;

class RbacController extends Controller {
    public function actionInit() {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Criar um post';
        $auth->add($createPost);

        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $createPost);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $author);          // admin herda tudo o que author pode
        $auth->assign($admin, 1);                  // user_id 1 = admin
    }
}
```

---

## 8. Esquema da BD — tabelas `user` + RBAC (slide 19 da aula)

```
user (id PK, username*, auth_key*, password_hash*, password_reset_token, email*, status*,
      created_at*, updated_at*, verification_token)           * = NOT NULL
   │ 1
   │ 1
user_profile (user_id PK/FK→user.id, nome, telefone, morada)   ← info extra (exemplo do slide; no VolLink: user_details)

migration (version PK varchar(180), apply_time)

auth_rule (name PK varchar(64), data blob, created_at, updated_at)
   ▲ rule_name
auth_item (name PK varchar(64), type smallint*, description text, rule_name FK, data blob, created_at, updated_at)
   ▲ parent / child                       ▲ item_name
auth_item_child (parent*, child*)    auth_assignment (item_name PK/FK, user_id varchar(64)*, created_at)
```
- `auth_item.type`: **1 = role**, **2 = permission**.
- `auth_assignment.user_id` é **`VARCHAR(64)`** (não inteiro) — atenção aos `JOIN` com `user.id` ([`07`](07-gridview-avancado-roles-por-user.md)).
- No VolLink, a "tabela extra 1:1" chama-se **`user_details`** ([`08`](08-aplicacao-ao-vollink.md)).

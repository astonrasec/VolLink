# Plataformas 02 — Core e Arquitetura do Yii2 (Aula 02 + Ficha 2)

> Fontes: `Aula 02 - Yii2 Components.pdf`, `PlatSI-Ficha-Prática-02.pdf`.
> Respostas às lacunas das fichas e exemplos "oficiais" assinalados como **(nossa)**.

---

## 1. O coração: `Yii::$app`

- **Instância única (Singleton)** da aplicação, acessível estaticamente via `\Yii::$app` em toda a app.
- **Gestor de componentes:** carrega classes essenciais só quando necessárias (**lazy loading**).
- **Raiz de configuração:** mapeia os arrays definidos em **`config/web.php`**.
- Componentes: `Yii::$app->request`, `->response`, `->db`, `->user`, (também `session`, `cookies`, `urlManager`, `cache`…).

---

## 2. Routing e `UrlManager`

- O encaminhamento é gerido pelo componente **`urlManager`**.
- Formato padrão **`ControllerID/ActionID`**.
- **Bidirecional:** as regras aplicam-se à **leitura** (URL de entrada) e à **escrita** (geração de links).
- **Pretty URLs:** oculta o `index.php?r=` mediante configuração.
```php
// config/web.php
'urlManager' => [
    'enablePrettyUrl' => true,
    'showScriptName'  => false,
    'rules' => [
    ],
],
```
> Requer `mod_rewrite` (Apache) e `.htaccess`/regras de reescrita no servidor.

### 2.1 Criar URLs — `yii\helpers\Url::to()`
Cria qualquer tipo de URL a partir de **rotas e parâmetros**; suporta **aliases** (convertidos primeiro na rota real).
```php
use yii\helpers\Url;

// 1. Rota básica          (/index.php?r=question/index)
echo Url::to(['question/index']);
// 2. Com parâmetros       (/index.php?r=question/view&id=100)
echo Url::to(['question/view', 'id' => 100]);
// 3. Com âncora HTML      (#content)
echo Url::to(['post/view', 'id' => 100, '#' => 'content']);
// 4. URL absoluto         (https://www.site.com/index.php?r=question/index)
echo Url::to(['question/index'], true);     // 'https' força protocolo seguro
```

### 2.2 Prefixo `action` (obrigatório)
- Um método só é acedível por rota se começar por **`action`** (minúsculo).
- **CamelCase ↔ hífen:** `actionCriarNova` → URL `criar-nova`.
- **Binding de parâmetros:** os argumentos do método são preenchidos automaticamente pelo `$_GET` correspondente.
```php
class QuestionController extends Controller {
    // URL: /question/create
    public function actionCreate() {
        $model = new Question();
        // …
        return $this->render('create', ['model' => $model]);
    }
    // O parâmetro $id é extraído da rota!
    public function actionView($id) { /* ... */ }
}
```

---

## 3. `Request` e `Response`

| `Yii::$app->request` | `Yii::$app->response` |
|---|---|
| Devolve **`null`** (sem avisos PHP) para variáveis inexistentes | Representa a **resposta HTTP** enviada ao cliente |
| **Proteção CSRF** nativa | Formatação direta: devolve **JSON** ou HTML |
| Identificação: `isPost`, `isAjax`, `isPjax` | Redirecionamentos limpos com `redirect()` |
| | Alterar códigos de estado HTTP |
```php
$req = Yii::$app->request;
$req->post('nome');                 // null se não existir
if ($req->isPost) { … }
if ($req->isAjax) { … }

$res = Yii::$app->response;
$res->format = \yii\web\Response::FORMAT_JSON;      // conversão automática para JSON (API)
return $this->redirect(['question/index']);         // redirecionamento 302 limpo
```

---

## 4. Session e Cookies

| `Yii::$app->session` (server-side) | `Yii::$app->cookies` (client-side) |
|---|---|
| Arranque automático | **Assinatura criptográfica HMAC** automática contra manipulação no cliente |
| **Flash messages:** dados que sobrevivem só até ao **próximo pedido** (mensagens de sucesso/erro após formulário) | Guardar preferências (idioma, tema) |
```php
Yii::$app->session->setFlash('success', 'Pergunta criada com sucesso!');

// Guardar cookie
Yii::$app->response->cookies->add(new \yii\web\Cookie([
    'name' => 'user_language', 'value' => 'pt'
]));
// Ler
Yii::$app->request->cookies->getValue('user_language');
```

---

## 5. Ligação à base de dados

- Baseada em **PDO**; **agnóstica** (MySQL, PostgreSQL, SQLite…); **Schema Caching** em produção (evita reler a estrutura das tabelas).
```php
// config/db.php
return [
    'components' => [          // (no template real devolve diretamente a definição do componente 'db')
        'db' => [
            'class'    => 'yii\db\Connection',
            'dsn'      => 'mysql:host=localhost;dbname=quiz',
            'username' => 'root',
            'password' => 'segredo',
            'charset'  => 'utf8mb4',
        ],
    ],
];
```
> **Nota (nossa):** no template o `config/db.php` devolve o array do componente (`'class'`, `'dsn'`, …) e o `config/web.php` faz `'db' => $db`. Credenciais reais **nunca** vão para o Git (usar variáveis de ambiente / `db-local.php` no template advanced).

---

## 6. Declaração de modelos: Eloquent vs ActiveRecord

| Laravel Eloquent | Yii2 ActiveRecord |
|---|---|
| `$fillable` define atributos permitidos (*mass assignment*) | **`rules()`** substitui o `$fillable` **e** valida |
| `$timestamps = false` (desativar) | Controlo por regras/behaviors |
```php
// Yii2
class Question extends ActiveRecord {
    public function rules() {
        return [
            [['text'], 'required'],
        ];
    }
}
```
**Relacionamentos 1-N:**
| Eloquent | Yii2 |
|---|---|
| Nome do método arbitrário; chave estrangeira inferida | **Prefixo `get`** define a propriedade virtual `$answers`; **mapeamento explícito** das chaves |
```php
class Question extends ActiveRecord {
    public function getAnswers() {
        return $this->hasMany(Answer::class, ['question_id' => 'id']);
    }
}
```

---

## 7. Ficha 2 — Actions, Views, formulários e BD

**Objetivos:** criar uma *action* e vista; formulário com validação (modelo + action + vista); configurar/usar BD; aceder a dados via **ActiveRecord**.

1. **"Hello World"** — seguir o guia oficial <http://www.yiiframework.com/doc-2.0/guide-start-hello.html>.
   Esboço **(nosso)**:
   ```php
   // controllers/SiteController.php
   public function actionSay($message = 'Hello') {
       return $this->render('say', ['message' => $message]);
   }
   ```
   ```php
   // views/site/say.php
   <?php use yii\helpers\Html; ?>
   <?= Html::encode($message) ?>
   ```
   → acessível em `?r=site/say&message=Olá`.
2. **Menu principal** com link para a página, **usando geração de URL** (com o parâmetro necessário à vista):
   ```php
   ['label' => 'Hello', 'url' => ['site/say', 'message' => 'Hello']],
   ```
   (em `views/layouts/main.php`, `Nav::widget` → `items`). Ver <http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html>.
3. **Registo de uma pessoa** (não são utilizadores da app): introduz um **modelo não ligado à BD** (`yii\base\Model`) com regras de validação e `validate()`, a API **`ActiveForm`** e o helper **`Html`**. Guia: <http://www.yiiframework.com/doc-2.0/guide-start-forms.html>.
   ```php
   // models/EntryForm.php
   class EntryForm extends \yii\base\Model {
       public $name; public $email;
       public function rules() {
           return [[['name', 'email'], 'required'], ['email', 'email']];
       }
   }
   ```
   ```php
   // SiteController
   public function actionEntry() {
       $model = new EntryForm();
       if ($model->load(Yii::$app->request->post()) && $model->validate()) {
           return $this->render('entry-confirm', ['model' => $model]);
       }
       return $this->render('entry', ['model' => $model]);
   }
   ```
   ```php
   // views/site/entry.php
   <?php $form = ActiveForm::begin(); ?>
       <?= $form->field($model, 'name') ?>
       <?= $form->field($model, 'email') ?>
       <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
   <?php ActiveForm::end(); ?>
   ```
4. **Links** entre as páginas para garantir o bom funcionamento.
5. **BD + ActiveRecord** — guia <http://www.yiiframework.com/doc-2.0/guide-start-databases.html> (ler primeiro as alíneas):
   - a. Criar a BD (se não existir), criar tabela e carregar dados (guia oficial: tabela `country` com `code`, `name`, `population`);
   - b. Configurar a ligação à BD;
   - c. **Em que ficheiro é feita a ligação?** → **`config/db.php`**, incluído por `config/web.php` (e `config/console.php` para a consola);
   - d. Criar, **por ordem**:
     1. **Modelo ActiveRecord.** *Como se torna um modelo em ActiveRecord?* → a classe **estende `yii\db\ActiveRecord`** e declara `public static function tableName()` (opcional se o nome da tabela coincidir com o convencionado);
     2. **Controlador próprio e ActionHandler** (ex.: `CountryController::actionIndex()` com `Country::find()->orderBy('name')->all()` e paginação);
     3. **Vista** que apresenta os dados.
6. **Menu principal** com link para a funcionalidade do ponto 5.

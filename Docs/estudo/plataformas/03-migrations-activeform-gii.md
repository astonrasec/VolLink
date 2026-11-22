# Plataformas 03 — Migrations, ActiveForm e Gii (Aula 03 + Ficha 3)

> Fontes: `Aula 03 - Yii2 ActiveForm + Gii.pdf`, `PlatSI-Ficha-Prática-03.pdf`.
> Respostas às perguntas da ficha: **(nossas)**, a partir do guia oficial do Yii2.

---

## 1. Migrations — controlo de versões da BD

- **Versionamento seguro:** como o Git faz ao código, as migrations gerem o histórico de alterações da BD (criar tabelas, adicionar colunas, remover índices).
- **Trabalho em equipa:** em vez de partilhar `.sql`, cada programador partilha o **ficheiro de migration**.
- **Rastreabilidade:** o Yii2 cria automaticamente a tabela **`migration`** com as migrations já aplicadas (e quando).

### 1.1 Operações
| Operação | Efeito |
|---|---|
| **create** | Gera ficheiro PHP vazio em `migrations/` com **timestamp** no nome (ordem cronológica) |
| **up** (`migrate`) | Lê a tabela `migration`, vê os ficheiros novos e executa-os |
| **down** | Reverte a última migration (`php yii migrate/down 3` reverte 3) |
| **redo** | `down` seguido de `up` — útil para testar a reversão |

Geridas **exclusivamente pelo terminal**, na raiz do projeto (onde está o ficheiro `yii`):
```bash
php yii migrate/create create_users_table   # nova migration
php yii migrate                              # aplicar
php yii migrate/down                         # reverter
php yii migrate/redo                         # refazer
php yii migrate/history                      # ver histórico (ficha)
```

### 1.2 `safeUp()` e `safeDown()` — estrutura transacional
- `safeUp()` — aplica a alteração; `safeDown()` — desfaz **exatamente** o que o `safeUp()` fez (**obrigatório** para se poder reverter).
- **Porquê "safe"?** O Yii2 corre-os dentro de uma **transação** de BD: se algo falhar a meio (ex.: erro na 3.ª tabela), há **rollback automático** → nada fica gravado, sem esquemas corrompidos.
- *(Ficha, alínea e)* **`up()/down()` vs `safeUp()/safeDown()`:** os primeiros correm **sem transação**; os `safe*` são **envolvidos numa transação** pelo Yii2, com rollback automático se algo falhar. ⚠ Em MySQL/MariaDB o DDL (`CREATE/ALTER TABLE`) faz *commit* implícito, pelo que o rollback pode não desfazer alterações de esquema já executadas — convém manter cada migration pequena e com um `safeDown()` correto.

### 1.3 Exemplo — criar uma tabela
```php
use yii\db\Migration;

class m231030_100000_create_users_table extends Migration {
    public function safeUp() {
        $this->createTable('users', [
            'id'         => $this->primaryKey(),
            'username'   => $this->string(50)->notNull()->unique(),
            'email'      => $this->string(255)->notNull()->unique(),
            'status'     => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
        ]);
    }
    public function safeDown() {
        $this->dropTable('users');       // reversão exata
    }
}
```

### 1.4 Exemplo — evoluir o esquema (coluna, índice, FK)
```php
public function safeUp() {
    $this->addColumn('users', 'last_login', $this->integer());                       // nova coluna
    $this->createIndex('idx-users-email', 'users', 'email');                          // índice
    $this->addForeignKey('fk-posts-user_id', 'posts', 'user_id', 'users', 'id', 'CASCADE');  // FK
}
public function safeDown() {                    // ordem inversa
    $this->dropForeignKey('fk-posts-user_id', 'posts');
    $this->dropIndex('idx-users-email', 'users');
    $this->dropColumn('users', 'last_login');
}
```
Outros métodos úteis: `batchInsert()` (carregar muitos registos), `insert()`, `dropTable()`, `renameColumn()`, `alterColumn()`.

---

## 2. Yii2 `ActiveForm`

**Formulários e validação** — gera tags HTML, **proteção CSRF** e **validação automática no cliente e no servidor**.
- **Integração nativa com modelos:** espelha a estrutura e as **regras de validação** do modelo (ActiveRecord ou `Model`) na interface; é uma ligação **objeto ↔ formulário**, não só um gerador de HTML.
- **Segurança incorporada:** injeta o **token CSRF** em formulários POST.
- Abre com `ActiveForm::begin()`, fecha com `ActiveForm::end()`.
- `$form->field($model, 'attr')` cria o campo no padrão **Bootstrap**: *label* + *input* + `div` para erros.
- Apresenta as mensagens de erro dos atributos do modelo.
```php
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'rememberMe')->checkbox() ?>
    <div class="form-group">
        <?= Html::submitButton('Login', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>
```

---

## 3. Gii — geração de código

- **Interface gráfica web integrada** (ao contrário do Laravel Artisan, só CLI): gera classes PHP preenchendo um formulário no browser.
- **Engenharia reversa da BD:** inspeciona chaves, tipos e *constraints*.
- Poupança de código repetitivo da arquitetura MVC; código dentro dos **standards** do Yii2.

### 3.1 Configuração e segurança
- **Só ativo em `YII_ENV_DEV`**; **só aceita `127.0.0.1`/`::1` por omissão** (`allowedIPs`).
```php
// só carrega o Gii se estiver em ambiente de desenvolvimento
if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],     // por defeito só o próprio PC
    ];
}
// Acesso: http://localhost/meusite/web/index.php?r=gii
```
> ⚠ **Risco de segurança:** nunca ativar o Gii num servidor de produção aberto à internet.

### 3.2 Os 6 geradores básicos (Ficha 3, ex. 3 — descrição nossa)
| Gerador | Para que serve |
|---|---|
| **Model Generator** | Gera uma classe de modelo (ActiveRecord) a partir de uma tabela |
| **CRUD Generator** | Gera controlador + vistas CRUD completas para um modelo |
| **Controller Generator** | Gera um controlador com as *actions* e vistas indicadas |
| **Form Generator** | Gera uma vista de formulário (e action) a partir de um modelo |
| **Module Generator** | Gera o esqueleto de um módulo Yii2 |
| **Extension Generator** | Gera a estrutura de uma extensão (pacote Composer) |

### 3.3 Model Generator (da BD à classe)
Entradas: **Table Name** (ex.: `product`) → sugere **Model Class** (`Product`). Gera ao ler o esquema:
- **Rules:** `NOT NULL` → `required`; `VARCHAR(50)` → `string, max=50`; `INT` → `integer`.
- **Attribute labels:** converte nomes técnicos (`date_of_birth`) em legíveis ("Date Of Birth").
- **Relations:** analisa as FKs e gera `getCategory()` (**hasOne**) ou `getProducts()` (**hasMany**) sem trabalho manual.

### 3.4 CRUD Generator ("canivete suíço")
Entradas obrigatórias: **Model** (ex.: `app\models\Product`) e **Controller** (ex.: `app\controllers\ProductController`). Gera:
| Ficheiro | Conteúdo |
|---|---|
| `ProductController.php` | actions `index`, `view`, `create`, `update`, `delete` |
| `ProductSearch.php` | classe de pesquisa/filtros (LIKE, intervalos) para grelhas |
| `views/product/index.php` | `GridView` com paginação e ordenação |
| `views/product/_form.php` | `ActiveForm` partilhado entre criação e edição |
(também `view.php`, `create.php`, `update.php`)

### 3.5 Ferramenta "Diff"
O Gii nunca escreve ficheiros sem aprovação: em **Preview** lista o que vai criar (verde); ao clicar num ficheiro abre diff lado a lado (vermelho = atual, verde = novo), permitindo aceitar só o essencial (ex.: nova coluna → atualizar só `rules()`).

---

## 4. Ficha 3 — Gii e Migrações

**Objetivos:** introdução ao Gii; reconhecer geradores; exercícios de geração/adaptação; migrações — criar, aplicar e reverter.

1. Verificar se o **Gii** está instalado (`composer show yiisoft/yii2-gii`; módulo ativo em `config/web.php`).
2. Aceder em `http://localhost/basic/web/index.php?r=gii`.
3. Explicar os 6 geradores → tabela 3.2.
4. **Criar a tabela `country` por migração** (em vez de manualmente na ficha anterior). Doc: <https://www.yiiframework.com/doc/guide/2.0/en/db-migrations>.
   - a) `DROP TABLE country;`
   - b) A consola lê a ligação à BD em **`config/console.php`** (que inclui `db.php`). Pode falhar com a web a funcionar porque a **config da consola é outra** (ficheiro/aliases/credenciais diferentes, ou `config/console.php` sem `db`).
   - c) `php yii migrate/create create_country_table` → ficheiro em **`migrations/`**; nome = `m<YYMMDD_HHMMSS>_create_country_table` (**timestamp**) → define a **ordem de execução** (e portanto a coerência dependências/FKs).
   - d) `safeUp()`/`safeDown()` para `country` com `id` (PK autoincrement), `code`, `name`, `population`:
     ```php
     public function safeUp() {
         $this->createTable('country', [
             'id'         => $this->primaryKey(),
             'code'       => $this->string(2)->notNull()->unique(),
             'name'       => $this->string(52)->notNull(),
             'population' => $this->integer()->notNull()->defaultValue(0),
         ]);
     }
     public function safeDown() { $this->dropTable('country'); }
     ```
   - e) `up()/down()` vs `safeUp()/safeDown()` → ver 1.2.
   - f) `php yii migrate`; confirmar no cliente de BD.
   - g) Tabela adicional: **`migration`** — guarda o nome (`version`) e `apply_time` de cada migração aplicada.
   - h) 2.ª migração: carregar **≥ 10 registos** com `batchInsert()`:
     ```php
     $this->batchInsert('country', ['code', 'name', 'population'], [
         ['PT', 'Portugal', 10300000], ['ES', 'Spain', 47400000], /* … ≥ 10 */
     ]);
     ```
   - i) `safeDown()` desta migração **não pode ser `dropTable`** (apagaria a tabela criada pela migração anterior): deve **apagar só os registos inseridos** (`$this->delete('country', ['code' => [...]])` ou `truncate`) — cada migração reverte apenas o que fez.
   - j) 3.ª migração (alterar esquema): (i) criar `continent (id, name)` e carregar dados; (ii) `addColumn('country', 'continent_id', ...)`; (iii) FK `country.continent_id → continent.id`.
   - k) **Ordem:** no `safeUp` primeiro cria-se/carrega-se `continent` (a FK precisa da tabela referenciada), depois a coluna, depois a FK; no `safeDown` inverte-se (drop FK → drop coluna → drop `continent`) porque não se pode apagar uma tabela/coluna com dependências ativas.
   - l) Comandos: `migrate/history` (lista as aplicadas), `migrate/down 1` (reverte 1), `migrate/redo` (down+up da última), `migrate` (reaplica pendentes).
   - m) Vantagens vs criação manual: versionamento no Git; reprodutibilidade em qualquer ambiente/equipa; reversibilidade (rollback); rastreabilidade (tabela `migration`); transações seguras.
5. **Scaffolding com Gii:** (i) Model `Country`; (ii) CRUD de `country` (controlador + vistas). Guia <http://www.yiiframework.com/doc-2.0/guide-start-gii.html>.
6. Criar **opções de menu** para a funcionalidade do ponto 5.
7. **Classificar** os elementos gerados (modelo, controlador, vista). Para além dos atributos, o ActiveRecord gerado tem **um método de relação** (`getContinent()`, `hasOne`) por causa da FK — devolve o registo relacionado; usado nas vistas (ex.: `$model->continent->name`) para mostrar o nome em vez do id.
8. **Diagrama do ciclo de vida MVC** por rota (`country/index`, `view`, `create`, `update`, `delete`): pedido → UrlManager → Controller::actionX → Model (ActiveRecord/Search) → View → Response.
9. Alterar o widget (5.ii), **gerar só a vista `index`** e analisar o código.
10. **Reescrever a `index` à mão:** (a) na action: `$countries = Country::find()->all();` (guia ActiveRecord); (b) na vista, ciclo **`foreach`** sobre `$countries`.
    ```php
    // controller
    public function actionIndex() {
        $countries = Country::find()->orderBy('name')->all();
        return $this->render('index', ['countries' => $countries]);
    }
    // view
    <?php foreach ($countries as $c): ?>
        <tr><td><?= Html::encode($c->name) ?></td><td><?= $c->population ?></td></tr>
    <?php endforeach; ?>
    ```

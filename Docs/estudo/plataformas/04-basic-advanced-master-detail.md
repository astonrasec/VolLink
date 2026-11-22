# Plataformas 04 — Basic vs Advanced Template e Master-Detail (Aula 04 + Ficha 4)

> Fontes: `Aula 04 - Yii2 Basic vs Advanced + Master Detail.pdf`, `PlatSI-Ficha-Prática-04.pdf`.
> ⚠ O nosso projeto VolLink tem **backend + frontend + API** → é uma estrutura de tipo **Advanced** (ver também o repositório).

---

## 1. Templates de projeto Yii2

Mesma framework, **estrutura de projeto diferente**:
| | **Basic** | **Advanced** |
|---|---|---|
| Comando | `composer create-project --prefer-dist yiisoft/yii2-app-basic basic` | `composer create-project --prefer-dist yiisoft/yii2-app-advanced advanced` |
| Organização | **Single-App (1)** | **Multi-App (3)** |
| Frontend/Backend | Juntos | **Separados** |
| Código partilhado | Dentro da aplicação | Em `common/` |
| Consola | Integrada | `console/` separada |
| Dimensão | Pequenos a médios, simples | Grandes e complexos (empresariais) |

**Porquê o Advanced?** À medida que cresce a complexidade (Frontend / Backend / Consola / Componentes comuns), o Basic concentra tudo numa só app; o Advanced **separa responsabilidades** e facilita a organização. A diferença fundamental é **como o código é organizado**.

### 1.1 Aplicações do Advanced
- **Frontend** e **Backend** — HTTP requests; cada uma tem **os seus controllers, views, configuração e entry script**; respondem a URLs diferentes, p.ex.: `frontend/[...]?r=product/index` (consulta de dados na página) e `backend/[...]?r=product/index`. São **independentes**, usam a **mesma BD** (o Backend pode não ser a API do Frontend).
- **`common/`** — componentes usados por ambas (modelos, config comum).
- **Consola** — tarefas por terminal: migrações, tarefas agendadas (backups), manutenção, scripts.
```php
// console/controllers/ReportController.php
namespace console\controllers;
use yii\console\Controller;

class ReportController extends Controller {
    public function actionGenerate() {
        // generate report
    }
}
// executar:  php yii report/generate
```
- **Environments** (Dev/Prod): `php init` — escolhe o ambiente e aplica a configuração correta.
- **Namespaces:** cada app tem o seu (`frontend\controllers`, `backend\controllers`, `common\models`, `console\controllers`) → permite dois componentes com o mesmo nome em namespaces diferentes (`frontend/controllers/ProductController` e `backend/controllers/ProductController`, ambos usando `common/models/Product`).
- Rotas exemplo: `frontend/[...]?r=product/view&id=25` e `backend/[...]?r=product/update&id=25`.

---

## 2. Master-Detail

**Padrão de design:** relação estrutural e visual entre uma entidade principal (**Master**) e as suas subentidades (**Detail**), normalmente refletida numa **relação 1:N** na BD. Objetivo: organizar a informação **hierarquicamente** para não sobrecarregar o utilizador.

Exemplos: 1 Cliente → N Encomendas; 1 Encomenda → N Artigos; 1 Questão → N Respostas; Fatura → Linhas; Departamento → Funcionários; Artigo de Blog → Comentários.
- **Master:** entidade principal e independente (contexto global, o registo "Pai").
- **Detail:** registos dependentes ("Filhos"); só fazem sentido associados ao Master — se o Master não existir ou for apagado, perdem a razão de ser.
- **Navegação típica:** selecionar um registo na grelha Master atualiza a área de Detail.

### 2.1 Fluxo de gravação (Master + Details criados em conjunto)
1. **Instanciar modelos:** um Master vazio e um array de Details.
2. **Gravar o Master** primeiro, para a BD gerar a PK (ID).
3. **Atribuir FKs:** injetar o ID do Master em cada Detail.
4. **Gravar os Details** (usar **transações** para segurança).

### 2.2 Sem Yii2 vs com Yii2
Sem framework: `cliente.php?id=12` → `SELECT * FROM cliente WHERE id=12;` + `SELECT * FROM encomendas WHERE cliente_id=12;` → renderiza.

Com Yii2 — dois ActiveRecords e relações em ambos os sentidos:
```php
class Cliente extends ActiveRecord {          // Master → Detail
    public function getEncomendas() {
        return $this->hasMany(Encomenda::class, ['cliente_id' => 'id']);
    }
}
class Encomenda extends ActiveRecord {        // relação inversa
    public function getCliente() {
        return $this->hasOne(Cliente::class, ['id' => 'cliente_id']);
    }
}
```
```php
// Controller
public function actionView($id) {
    $cliente    = Cliente::findOne($id);
    $encomendas = $cliente->encomendas;               // acesso pela relação
    return $this->render('view', ['cliente' => $cliente, 'encomendas' => $encomendas]);
}
```
```php
<!-- View -->
<h1><?= Html::encode($cliente->name) ?></h1>
<?php foreach ($encomendas as $enc): ?>
    <?= $enc->id ?> <?= $enc->total ?>
<?php endforeach; ?>
```
> No slide a relação inversa aparece como `[id' => ‘cliente_id']` (aspas partidas) — o correto é `['id' => 'cliente_id']`.

### 2.3 Criar um Detail associado ao Master
```php
public function actionCriarEncomenda($clienteId) {
    $novaEncomenda = new Encomenda();
    $novaEncomenda->cliente_id = $clienteId;

    if ($novaEncomenda->load(Yii::$app->request->post())) {   // (slide usa $model — gralha)
        $novaEncomenda->save();
        return $this->redirect(['cliente/view', 'id' => $clienteId]);
    }
    return $this->render('criar-encomenda', ['novaEncomenda' => $novaEncomenda]);
}
```

### 2.4 Lazy loading vs Eager loading
| Lazy | Eager |
|---|---|
| Carrega a relação só **quando é acedida** | Carrega a relação **na query inicial** (`with()`) |
```php
// Lazy (1 query + N queries — problema "N+1")
$customers = Customer::find()->all();
foreach ($customers as $customer) { echo count($customer->orders); }

// Eager (2 queries no total)
$customers = Customer::find()->with('orders')->all();
```
Regra: **lazy** quando a relação pode não ser necessária; **eager** quando se sabe que os dados relacionados vão ser usados/apresentados.

---

## 3. Ficha 4 — Aplicação de Blog (Master-Detail com Gii)

**Objetivos:** desenvolver uma app de blogging em Yii2: análise de requisitos, design, configurar/desenvolver a BD, protótipo inicial via **scaffolding (Gii)**, Posts, Comentários, adaptações finais, testes de utilização.

### 3.1 Passos
a. Ler o enunciado; b. mockups de alto nível; c. desenvolver a BD; d. gerar código (scaffolding); e. manipular o código gerado; f. testar.
> **Sem autenticação de utilizadores** nesta ficha (foco nos requisitos).

### 3.2 Requisitos
- **CRUD dos posts** (dono do blog) · **ver posts e criar um post** · **criar comentários**.
- Outros:
  - i. Página principal apresenta os **posts existentes**;
  - ii. Cada post apresenta os **respetivos comentários** — usar os **métodos de relacionamento** gerados nos modelos;
  - iii. A informação da página principal **não usa widgets Yii** (é para os leitores do blog);
  - iv. Gestão de posts e comentários com **entradas próprias no menu** (opções do administrador/dono). **O admin não pode criar posts nem comentários** → remover o botão e o método `create` na gestão.

### 3.3 Diagrama ER (da ficha)
```
 post                          comment
 ─────────────                 ─────────────
 id (PK)            1 ◇──── *  id (PK)
 title                         content
 content                       createtime
 tags                          idpost (FK → post.id)
 createtime
```
### 3.4 Esboço de migração (nosso)
```php
$this->createTable('post', [
    'id' => $this->primaryKey(),
    'title' => $this->string()->notNull(),
    'content' => $this->text()->notNull(),
    'tags' => $this->string(),
    'createtime' => $this->dateTime(),
]);
$this->createTable('comment', [
    'id' => $this->primaryKey(),
    'content' => $this->text()->notNull(),
    'createtime' => $this->dateTime(),
    'idpost' => $this->integer()->notNull(),
]);
$this->addForeignKey('fk-comment-idpost', 'comment', 'idpost', 'post', 'id', 'CASCADE');
```
Depois: Gii → Model `Post`, `Comment`; CRUD para ambos; página pública `index` (foreach sobre `Post::find()->with('comments')->all()`); criar comentário numa action `actionComentar($idpost)` (padrão da secção 2.3).

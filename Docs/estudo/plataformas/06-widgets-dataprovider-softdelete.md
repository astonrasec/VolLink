# Plataformas 06 — Widgets, Data Providers e Soft Delete (Aula 06 + Ficha 6)

> Fontes: `Aula 06 - Yii2 Widgets.pdf`, `PlatSI-Ficha-Prática-06.pdf`.
> ⚠ O **Soft Delete** do CRUD de utilizadores (back-office) é **requisito explícito da Fase 1** do enunciado.

---

## 1. Widgets — apresentação de dados

Fluxo: **Pedido → Rota → Controller (action) → Model (opcional) → View → Widgets.**
- **Widgets** = componentes reutilizáveis que facilitam a apresentação de dados na View.
- Exemplo: o utilizador pede a página de cursos → o Controller obtém os cursos pelo Model → envia à View → a View usa um widget (`ListView`, `GridView`) para os mostrar.

## 2. Data Provider

Componente que **fornece os dados aos widgets**, com **pesquisa, ordenação e paginação**. `ListView` e `GridView` **usam** Data Providers.
```php
class PostController extends Controller {
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query'      => Post::find(),
            'pagination' => ['pageSize' => 5],
            'sort'       => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}
```
```php
<?php use yii\grid\GridView; ?>
<?= GridView::widget(['dataProvider' => $dataProvider]) ?>
```

### 2.1 Tipos
| Data Provider | Fonte | Caso de uso | Performance |
|---|---|---|---|
| **ActiveDataProvider** | Modelos Active Record (queries) | CRUDs e listagens | Média (instancia objetos) |
| **SqlDataProvider** | SQL *raw* | Relatórios complexos, dashboards | Alta (queries diretas) |
| **ArrayDataProvider** | Arrays em memória | Dados que não pertencem à BD | Baixa (dados em memória) |

**ActiveDataProvider** — configurado por query; cálculo automático do total de registos. Componentes: `query`, `pagination` (nº por página), `sort` (ordem padrão; é preciso indicar o campo).

---

## 3. Widgets principais

### 3.1 `DetailView` — um único modelo
- Detalhes de **uma instância**; organiza em **tabela vertical** (atributo à esquerda, valor à direita); escolhe-se que atributos; **sufixo de formato** (`email`, `datetime`, `html`…); permite `label` e `value` manuais.
```php
use yii\widgets\DetailView;

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'nome',
        'descricao',
        'duracao',
        'email:email',
        'created_at:datetime',
        [
            'label' => 'Estado',
            'value' => $model->active ? 'Ativo' : 'Inativo',
        ],
    ],
]);
```

### 3.2 `ListView` — vários registos em lista
- Cada registo é desenhado por uma **view parcial** (`itemView`); personalizável; usa Data Provider. Bom para **cartões, notícias, produtos, resultados de pesquisa**.
```php
use yii\widgets\ListView;

$dataProvider = new ActiveDataProvider([
    'query' => Curso::find(),
    'pagination' => ['pageSize' => 10],
]);

echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_curso',
]);
```
```php
<!-- views/curso/_curso.php (em cada item, $model = o registo) -->
<div class="curso">
    <h3><?= $model->nome ?></h3>
    <p><?= $model->descricao ?></p>
</div>
```
Mostra "Showing 1-3 of 15 items." e paginador `« 1 2 3 »`.

### 3.3 `GridView` — vários registos em tabela
- Linha = registo; coluna = atributo; colunas configuráveis; usa Data Provider; ideal para **painéis administrativos**; **paginação nativa**; **filtros e ordenação nos cabeçalhos**.
```php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => ['id', 'nome', 'duracao', 'estado'],
]);
```

### 3.4 Comparação
| Widget | Objetivo | Dados recebidos | Apresentação |
|---|---|---|---|
| DetailView | Consultar **um** registo | **Modelo** | Tabela atributo/valor |
| ListView | Vários registos | Data Provider | Lista personalizada |
| GridView | Vários registos | Data Provider | Tabela |

---

## 4. Soft Delete

| Soft delete | Hard delete |
|---|---|
| Ocultação **lógica**; preserva a integridade estrutural; **recuperação imediata** | Remoção física total; perde histórico relacional; **irreversível** |

Técnica: o registo é **marcado como eliminado** em vez de removido; continua na BD; **as consultas normais devem excluir** os marcados.

**Cuidados:**
- Definir se as **relações com registos eliminados** continuam acessíveis.
- **Impedir** utilizadores sem permissão de consultar/restaurar eliminados.
- Decidir se a eliminação **propaga** a entidades relacionadas.

### 4.1 Implementação típica no Yii2 (3 passos)
1. **Tabela `user`:** usar coluna existente **`status`** (`0 = Eliminado`, `9 = Inativo`, `10 = Ativo`) **ou** adicionar `deleted_at` (timestamp).
2. **Sobrescrever o modelo** — substituir `delete()`:
   ```php
   public function delete() {
       $this->status = 0;
       return $this->save(false);
   }
   ```
3. **Filtro de consulta** — ajustar o `ActiveQuery` (ex.: `where deleted_at IS NULL`; no caso de status: excluir `status = 0`).

### 4.2 Esboço para o `User` do template Advanced (nosso)
```php
// common/models/User.php
const STATUS_DELETED  = 0;
const STATUS_INACTIVE = 9;
const STATUS_ACTIVE   = 10;

public function delete() {
    $this->status = self::STATUS_DELETED;
    return $this->save(false);
}

// listagem do back-office: esconder eliminados
$query = User::find()->where(['!=', 'status', User::STATUS_DELETED]);
```
O `findIdentity()` do template já usa `'status' => self::STATUS_ACTIVE` → um utilizador "eliminado" **deixa de poder autenticar-se** automaticamente.

---

## 5. Ficha 6 — Master-Detail com Widgets

**Objetivos:** Master-Detail com Widgets; **campos lookup** na GridView; **dropdowns ligados ao ActiveRecord**; widget **ListView**.

### 5.1 Modelo de dados (ficha, ponto 1) — template **advanced**
```
 genre (id INT PK, name VARCHAR(150))
   │ 1
   │ N
 book  (id INT PK, name VARCHAR(255), isbn VARCHAR(80), genre_id INT FK→genre.id)
   │ 1
   │ N
 chapter (id INT PK, name VARCHAR(200), book_id INT FK→book.id)
```
Passos:
2. Gii → modelos (com relacionamentos) e CRUD de `book`, `chapter` (e `genre`).
3. O **controlador do detail** (chapter) depende do **master** (book): redefinir `actionIndex($bookid)`:
```php
public function actionIndex($bookid) {
    $book = Book::findOne($bookid);
    $dataProvider = new ActiveDataProvider([
        'query' => $book->getChapters(),
    ]);
    return $this->render('index', ['dataProvider' => $dataProvider, 'book' => $book]);
}
```
4. Alterar o **botão de detalhe** da `GridView` de `book` para enviar o id do livro ao `index` dos capítulos (via `ActionColumn::urlCreator`):
```php
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id', 'name', 'isbn',
        [
            'class' => ActionColumn::className(),
            'urlCreator' => function ($action, Book $model, $key, $index, $column) {
                if ($action == 'view') {
                    return Url::toRoute(['chapter/index', 'bookid' => $model->id]);
                }
                return Url::toRoute([$action, 'id' => $model->id]);
            },
        ],
    ],
]); ?>
```
5. Testar o botão (mostra os capítulos do livro).
6. Atualizar o `index` dos capítulos para mostrar **a informação do livro**.
7. `actionCreate($bookid)` — recebe o id do livro:
```php
public function actionCreate($bookid) {
    $model = new Chapter();
    $model->book_id = $bookid;

    if ($this->request->isPost) {
        if ($model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
    } else {
        $model->loadDefaultValues();
    }
    return $this->render('create', ['model' => $model]);
}
```
8. **Remover/esconder** o campo `book_id` do formulário de criação.
9. Na `index` dos capítulos, link de criação com o id do livro:
```php
<?= Html::a('Create Chapter', ['create', 'bookid' => $book->id], ['class' => 'btn btn-success']) ?>
```
10. **Dropdown** do género na criação de livro (data binding ao modelo `Genre`):
```php
<?= $form->field($model, 'genre_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(\frontend\models\Genre::find()->asArray()->all(), 'id', 'name')) ?>
```
   `ArrayHelper::map()` extrai **id** (→ `value` do `<option>`) e **name** (→ texto visível).
11. **Campo lookup** na `GridView` dos livros (mostrar o nome do género):
```php
[
    'label' => 'Genre',
    'value' => function ($model) { return $model->genre->name; },
],
```
12. **ListView** — redefinir `BookController::actionIndex`:
```php
public function actionIndex() {
    $dataProvider = new ActiveDataProvider([
        'query' => Book::find(),
        'pagination' => ['pageSize' => 20],
    ]);
    return $this->render('index', ['dataProvider' => $dataProvider]);
}
```
13. Na vista `index`:
```php
<?= ListView::widget(['dataProvider' => $dataProvider, 'itemView' => '_book']) ?>
```
14. Template do registo (`_book.php`):
```php
<div class="book">
    <?= Html::encode($model->name) ?>
    <p><small><?= Html::encode($model->isbn) ?></small></p>
    <p><?= Html::a('Chapters', ['chapter/index', 'bookid' => $model->id], ['class' => 'btn btn-success']) ?></p>
</div>
```
> Nota (nossa): `Html::encode()` em dados do utilizador previne **XSS**.

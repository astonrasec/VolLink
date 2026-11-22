# Plataformas 07 — GridView avançado, SearchModel e Roles por Utilizador (Aula 07 + Aula 08)

> Fontes: `Aula 07 - Yii2 Widgets_GridView_PersonalizarColunas.pdf`, `Aula 08 - Yii2_RolesByUser.pdf`.
> Aplica-se diretamente ao **CRUD de utilizadores do back-office** (listar utilizadores com os seus *roles*, filtrar por role, esconder botões por permissão).

---

## 1. O papel do `SearchModel`

- **Herança Gii:** estende do modelo principal (gerado com o CRUD: `ProductSearch`, `UserSearch`…).
- **Filtros de tabela:** processa os inputs dos cabeçalhos da `GridView`.
- **Devolve um `ActiveDataProvider`** dinamicamente filtrado.

### 1.1 Integração no controller
Fluxo: capturar parâmetros GET → aplicar condições na `ActiveQuery` → exportar o `DataProvider` para a view.
```php
public function actionIndex() {
    $searchModel  = new ProductSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
        'searchModel'  => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}
```

### 1.2 Regras de validação — o atributo `safe`
- Define **que campos são permitidos para pesquisa**; bloqueia injeção de parâmetros indesejados (*mass assignment*).
- ⚠ **Sem regra de validação, o filtro da GridView é totalmente ignorado.**
```php
public function rules() {
    return [
        [['id', 'status'], 'integer'],
        [['name', 'created_at'], 'safe'],
    ];
}
```

### 1.3 O método `search()` — `andFilterWhere()` vs `andWhere()`
- Constrói a SQL a partir dos inputs.
- **`andFilterWhere()`** ignora valores **vazios** (`""`, `null`, `[]`), evita `WHERE nome = ''`, e reconhece **`0` como válido** (ao contrário do `empty()` do PHP).
- **`andWhere()`** com formulário vazio gera `WHERE nome = ''` → a BD devolve **0 registos**; o `andFilterWhere` descarta a cláusula e **lista todos**.
```php
$query->andFilterWhere([
    'id'     => $this->id,
    'status' => $this->status,
]);
$query->andFilterWhere(['like', 'name', $this->name]);
```
Esqueleto completo (padrão do Gii — nosso):
```php
public function search($params) {
    $query = Product::find();
    $dataProvider = new ActiveDataProvider(['query' => $query]);

    $this->load($params);
    if (!$this->validate()) {
        return $dataProvider;                 // sem filtros se inválido
    }
    $query->andFilterWhere(['id' => $this->id, 'status' => $this->status]);
    $query->andFilterWhere(['like', 'name', $this->name]);
    return $dataProvider;
}
```

### 1.4 Filtrar por relações
```php
// 1. JOIN com a tabela relacionada
$query->joinWith(['category']);
// 2. Filtrar pelo atributo da relação
$query->andFilterWhere(['like', 'category.name', $this->category_name]);
```
(O atributo virtual `category_name` é declarado no SearchModel e tem de estar em `rules()` como `safe`.)

---

## 2. Personalização de colunas da `GridView`

Controlar **campos, formatos e ações** apresentados.

### 2.1 `DataColumn` — propriedades principais
| Propriedade | Função |
|---|---|
| `attribute` | Atributo do modelo |
| `label` | Texto do cabeçalho |
| `format` | Formatação (`html`, `email`, `currency`, …); `raw` para HTML |
| `value` | **Expressão ou função anónima** (recebe o *model* da linha) — ex.: valor calculado ou da relação |
```php
[
    'attribute' => 'category_id',
    'label'     => 'Categoria',
    'value'     => function ($model) { return $model->category->name; },
],
[
    'attribute' => 'price',
    'format'    => ['currency', 'EUR'],
],
```
> Uma coluna pode **não corresponder** diretamente a um atributo da tabela.

### 2.2 Filtros personalizados — dropdown
Com um **array** na propriedade `filter`, o Yii gera um `<select>`: a **chave** vai para a SQL, o **valor** é o texto visível (substitui a caixa de texto livre).
```php
[
    'attribute' => 'status',
    'filter' => [1 => 'Ativo', 0 => 'Inativo'],
],
```
**Dinâmico com `ArrayHelper::map()`** (opções sincronizadas com a BD):
```php
use yii\helpers\ArrayHelper;
use app\models\Category;

[
    'attribute' => 'category_id',
    'filter' => ArrayHelper::map(Category::find()->asArray()->all(), 'id', 'name'),
]
```

### 2.3 `ActionColumn`
Botões por omissão: **View** → `actionView`; **Update** → `actionUpdate`; **Delete** → `actionDelete` **via POST**.
- **`template`:** ordem e presença dos botões; **`buttons`:** HTML por cada placeholder.
```php
[
    'class'    => ActionColumn::class,
    'template' => '{view} {update} {print}',
    'buttons'  => [
        'print' => function ($url, $model) {
            return Html::a('', ['print', 'id' => $model->id]);
        },
    ],
]
```
- **`visibleButtons`:** avaliação **linha a linha**; oculta botões sem destruir a coluna; **ideal para permissões RBAC**.
```php
[
    'class' => ActionColumn::class,
    'visibleButtons' => [
        'update' => function ($model, $key, $index) { return Yii::$app->user->can('edit'); },
        'delete' => function ($model) { return $model->status !== 'locked'; },
    ],
]
```

### 2.4 Adaptar o código do Gii
O Gii é só o início:
1. Remover colunas desnecessárias (ids internos, `created_at`).
2. Mudar *labels* para a linguagem do utilizador final.
3. Substituir FKs numéricas pelos **nomes relacionados**.

**Erros comuns:**
1. Esquecer a regra **`safe`** no SearchModel para campos novos.
2. **Problema N+1** (aceder a relações na grid sem `joinWith`).
3. Devolver badges/tags HTML sem `'format' => 'raw'`.

---

## 3. Roles por utilizador — `getRolesByUser()` (Aula 08)

```php
Yii::$app->authManager->getRolesByUser($userId);
```
- **Input:** `$userId` — PK do utilizador.
- **Output:** **array associativo** de objetos `yii\rbac\Role`; chave = **nome do role**, valor = objeto completo.
```php
[
    'admin' => yii\rbac\Role Object (
        ['type'] => 1,
        ['name'] => 'admin',
        ['description'] => 'Administrador Geral',
        ['ruleName'] => null,
        ['data'] => null,
        ['createdAt'] => 1633021200
    ),
    'editor' => yii\rbac\Role Object (...)
]
```

### 3.1 Apresentar os roles na GridView (*badges*)
Consulta os cargos do utilizador da linha → mapeia o array de `Role` para HTML puro; **`'format' => 'raw'` é obrigatório**.
```php
[
    'label'  => 'Cargos',
    'format' => 'raw',
    'value'  => function ($model) {
        $roles = Yii::$app->authManager->getRolesByUser($model->id);
        $badges = array_map(function ($role) {
            return Html::tag('span', $role->name, ['class' => 'label label-primary']);
        }, $roles);
        return implode(' ', $badges);
    },
]
```

### 3.2 Filtrar por relações múltiplas (1:n ou n:m)
| Projetos ↔ Tecnologias | Users ↔ Roles |
|---|---|
| `public $tec_id;` (atributo *safe*); `$query->joinWith('tecnologias');` `andFilterWhere(['projeto_tecnologia.tecnologia_id' => $this->tec_id])` | `public $role_name;` (*safe*); `$query->joinWith('assignments');` `andFilterWhere(['auth_assignment.item_name' => $this->role_name])` |
> A relação `assignments` é **mapeada previamente no modelo `User`** (nosso esboço):
```php
// common/models/AuthAssignment.php — ActiveRecord para a tabela auth_assignment (gerado com o Gii)
// common/models/User.php
public function getAssignments() {
    return $this->hasMany(AuthAssignment::class, ['user_id' => 'id']);
}
```
(O `user_id` em `auth_assignment` é `VARCHAR` no esquema RBAC do Yii; se o `JOIN` não casar em algum SGBD, converter/`CAST` o id.)

### 3.3 Relações n:m — o perigo das linhas duplicadas
O `JOIN` com uma relação n:m multiplica linhas (um projeto com 3 tecnologias aparece **3 vezes**). Solução: **`groupBy`** pela PK:
```php
// ProjectSearch::search()
$query->joinWith(['technologies']);
$query->andFilterWhere(['technology.id' => $this->tech_id]);
$query->groupBy('project.id');          // previne duplicação visual
```

### 3.4 Filtrar utilizadores por role
```php
// UserSearch::search()
$query->joinWith(['assignments']);
$query->andFilterWhere(['auth_assignment.item_name' => $this->role]);
```
A **dropdown** da GridView preenche a variável virtual `$this->role` do SearchModel. (Resultado final: grid com coluna de cargos e filtro por cargo.)

---

## 4. Resumo para o back-office do VolLink
| Necessidade | Ferramenta |
|---|---|
| Listar utilizadores com filtros | `UserSearch` + `GridView` + `andFilterWhere` |
| Mostrar o(s) cargo(s) | `getRolesByUser()` + `format => 'raw'` |
| Filtrar por cargo | `joinWith('assignments')` + `auth_assignment.item_name` (+ `groupBy` se necessário) |
| Esconder botões a quem não pode | `visibleButtons` + `Yii::$app->user->can(...)` |
| Apagar | `delete()` sobrescrito (**soft delete**), ver [`06`](06-widgets-dataprovider-softdelete.md) |

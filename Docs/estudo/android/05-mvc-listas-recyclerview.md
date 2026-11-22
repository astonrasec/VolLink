# Android 05 — MVC, ListView, GridView e RecyclerView (+ Ficha 5 "Books")

> Fontes: `05_Desenho_Interfaces_Graficas_ListView_GridView_RecyclerView.pdf` (Cap. 5), `05_Books_RecyclerView.pdf` (Ficha 5).
> ⚠ **O enunciado valoriza o padrão MVC** e exige atividades "dinâmicas" com dados do webservice — a `RecyclerView` + Adapter é a peça central da listagem.

---

## 1. Arquitetura MVC

**Model-View-Controller** — padrão que separa a *representação da informação* da *interação do utilizador*. Ideias centrais: **reutilização de código** e **separação de conceitos**.

| Camada | Papel |
|---|---|
| **Modelo** | Dados da app, regras de negócio, lógica e funções |
| **Vista** | Qualquer saída/representação dos dados (lista, tabela, diagrama); pode haver várias vistas do mesmo dado |
| **Controlador** | Medeia a entrada, convertendo-a em comandos para o modelo ou para a vista |

**Em Android (convenção das aulas):** package `modelo` (classes de domínio + gestores/Singleton), **Activities/Fragments + layouts XML** como Vista/Controlador, `adaptadores` para ligar modelo↔vista.

---

## 2. ListView, GridView e RecyclerView

- Listas e grelhas são muito usadas em móvel → três `ViewGroup` dedicados: **ListView**, **GridView**, **RecyclerView**.
- **ListView:** lista com deslocamento **vertical**; gere elementos visíveis, os fora da área visível e a reutilização dos objetos.
- **GridView:** grelha bidimensional (linhas/colunas), gestão a cargo do componente.
- Nestes dois **não se configuram os componentes visuais diretamente** → é preciso um **Adapter**.

### 2.1 Adapters
- **Intermediários entre o modelo e as vistas**: obtêm a informação do modelo e fornecem-na às vistas; criam uma *view* para cada item do conjunto de dados.
- Podem usar-se adaptadores prontos (`ArrayAdapter`, `CursorAdapter`, `SimpleAdapter`…) ou criar um **personalizado** com base em **`BaseAdapter`**.

### 2.2 Exemplo — Gestor de Contactos (MVC)
Modelo (package `modelo`):
```java
public class Contacto {
    private long id;
    private String nome;
    public Contacto(long id, String nome) { this.id = id; this.nome = nome; }
    public long getId() { return id; }
    public String getNome() { return nome; }
    @Override public String toString() { return "Id: " + id + " Nome: " + nome; }
}

public class GestorContactos {
    private LinkedList<Contacto> contactos;
    public GestorContactos() { contactos = new LinkedList<>(); adicionarDadosIniciais(); }
    private void adicionarDadosIniciais() {
        contactos.add(new Contacto(1001, "Ana Silva"));
        contactos.add(new Contacto(1002, "Joao Silva"));
        contactos.add(new Contacto(1003, "Maria Santos"));
    }
    public LinkedList<Contacto> getContactos() { return new LinkedList<>(contactos); }
    public Contacto getContacto(int index) { return contactos.get(index); }
    @Override public String toString() {
        StringBuilder sb = new StringBuilder();
        for (Contacto c : contactos) sb.append(c).append("\n");
        return sb.toString();
    }
}
```
**Problema:** com uma `TextView` com tudo concatenado *não é possível selecionar apenas 1 contacto*. **Solução:** `ListView` + `ArrayAdapter`.

### 2.3 `ArrayAdapter`
`public ArrayAdapter(Context context, int resource, T[] objects)`
- `context` → contexto atual; `resource` → id do layout usado para instanciar cada View; `objects` → os objetos a representar.
```java
public class ListViewContactosActivity extends AppCompatActivity {
    private GestorContactos gestorContactos;
    private ListView listViewContactos;
    private ArrayAdapter<Contacto> adaptador;

    @Override protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_list_view_contactos);
        setTitle("Lista de Contactos");
        gestorContactos = new GestorContactos();
        listViewContactos = findViewById(R.id.listViewContactos);
        atualizarContactos();
    }

    private void atualizarContactos() {
        adaptador = new ArrayAdapter<Contacto>(this,
                android.R.layout.simple_list_item_1,    // layout de cada item
                gestorContactos.getContactos());        // lista de dados
        listViewContactos.setAdapter(adaptador);        // atribuir o adapter
    }
}
```
Cada item é uma `TextView` (o `ArrayAdapter` **não permite alterar a view de cada item**) → para layout específico por item, usar `BaseAdapter` personalizado.

### 2.4 Selecionar um item (click listener) e abrir os detalhes
```java
listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
    @Override
    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
        Intent intent = new Intent(getApplicationContext(), DetalhesContacto.class);
        intent.putExtra(DetalhesContacto.POSICAO, position);
        startActivity(intent);
    }
});

public class DetalhesContacto extends AppCompatActivity {
    public static final String POSICAO = "amsi.dei.estg.ipleiria.pt.POSICAO";
    private GestorContactos gestorContactos;
    private Contacto contacto;
    private TextView textViewId, textViewNome;

    @Override protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_detalhes_contacto);
        setTitle("Detalhes de contacto");
        textViewId   = findViewById(R.id.textViewId);
        textViewNome = findViewById(R.id.textViewNome);
        gestorContactos = new GestorContactos();
        int posicao = getIntent().getIntExtra(POSICAO, 0);
        contacto = gestorContactos.getContacto(posicao);
        if (contacto != null) preencherContacto();
    }
    private void preencherContacto() {
        textViewId.setText("" + contacto.getId());
        textViewNome.setText(contacto.getNome());
    }
}
```
Este é o padrão **master/detail** exigido pelo projeto (aqui local; no projeto, com dados da API).

### 2.5 GridView
- Atividade com `GridView`; por omissão aparece como lista → definir `android:numColumns="auto_fit"` (XML ou propriedades).
- Pode usar `ArrayAdapter` ou um package `adapters` com adaptador personalizado.

### 2.6 `BaseAdapter` personalizado — 4 métodos
```java
public class ContactoAdapter extends BaseAdapter {
    @Override public int getCount()                       { /* total de elementos */ return 0; }
    @Override public Object getItem(int position)         { /* item nessa posição */ return null; }
    @Override public long getItemId(int position)         { /* id do item */ return 0; }
    @Override public View getView(int position, View convertView, ViewGroup parent) {
        // criar uma view para cada item a partir do layout específico do item
        return null;
    }
}
```
Passos: classe que herda `BaseAdapter` → implementar 4 métodos → atributos (contexto, lista) → construtor.

---

## 3. RecyclerView

- `ListView`/`GridView` são os ViewGroup base mas estão **deprecated** → usar **`RecyclerView`** (introduzido no Android Lollipop, API 21): mais avançada e flexível.
- É um **container** que encapsula um **`LayoutManager`** e um **`ItemAnimator`** e comunica com `RecyclerView.Adapter`.

### 3.1 Dependência (Gradle)
Antes (até API 28): `implementation 'com.android.support:recyclerview-v7:28.0.0'`  
Atualmente (Jetpack):
```kotlin
dependencies {
    implementation("androidx.recyclerview:recyclerview:1.4.0")
    // controlo de seleção touch/rato:
    implementation("androidx.recyclerview:recyclerview-selection:1.2.0")
}
```

### 3.2 LayoutManagers
O RecyclerView **não posiciona** os elementos; delega:
| LayoutManager | Resultado |
|---|---|
| `LinearLayoutManager` | Listas horizontais ou verticais |
| `GridLayoutManager` | Grelhas |
| `StaggeredGridLayoutManager` | Grelhas com itens de tamanhos variados (mosaico) |

### 3.3 Adapter personalizado
- Herda de `RecyclerView.Adapter<…ViewHolder>`; usa um **`ViewHolder`** (melhora a performance — "cache" de views).
- 3 métodos:
  - `getItemCount()` — nº de elementos
  - `onCreateViewHolder()` — devolve nova instância do ViewHolder
  - `onBindViewHolder()` — preenche cada item com dados
- Atribuir com `recyclerView.setAdapter(adapter)`.

### 3.4 Animações
- `ItemAnimator`; animações padrão com `notifyItemInserted(int position)` e `notifyItemRemoved(int position)` (em vez de `notifyDataSetChanged`, que não anima).

### 3.5 Desafio do capítulo
Gestor de contactos com `RecyclerView`: layout de item (imagem + contacto; o tipo da imagem é o **id do drawable**) + Adapter personalizado que usa esse layout.

### 3.6 Próximo tema
Menus e Settings (ver `06-menus-preferences.md`): Menus, Action Bar, ação *Up*, **SharedPreferences**.

---

## 4. Ficha 5 — *Books*: RecyclerView

**Objetivos:** apresentar os livros numa lista; usar `RecyclerView` e Adapters. (Continuar o projeto anterior; guardar cópia se quiser.)

### Passos
1. **Apagar** `EstaticoFragment`.
2. Alterar os títulos de `menu_main.xml` (e `strings.xml`) conforme a figura; 1.º item = **"Livros (Lista)"**, 2.º = **grelha**.
3. Dependência no `build.gradle.kts (Module:app)` + *Sync*:
   ```kotlin
   dependencies { implementation("androidx.recyclerview:recyclerview:1.4.0") }
   ```
4. Novo **`ListaLivrosFragment`** (Fragment Blank).
   - 4.1 Layout: um `RecyclerView` com id **`rvLivros`**.
   - 4.2 Renomear `fragment_dinamico.xml` → **`item_lista_livro.xml`** (representa **cada item**).
   - 4.3 No `SingletonGestorLivros`, método **`gerarLivros()`** (chamado no construtor) com 10 livros — construtor atual de `Livro`: `(id, capa, ano, titulo, autor, serie)`:
     ```java
     livros.add(new Livro(1,  R.drawable.programarandroid2, 2024, "Programar em Android AMSI - 1", "AMSI TEAM", "2ª Temporada"));
     livros.add(new Livro(2,  R.drawable.programarandroid1, 2024, "Programar em Android AMSI - 2", "AMSI TEAM", "2ª Temporada"));
     livros.add(new Livro(3,  R.drawable.logo_ulo,          2024, "Programar em Android AMSI - 3", "AMSI TEAM", "2ª Temporada"));
     // ... alternando as 3 imagens até ao livro 10 (Android AMSI - 10, programarandroid2)
     ```
5. **Package `adaptadores`** com **`ListaRecyclerAdaptador`**:
   - 5.1 `public class ListaRecyclerAdaptador extends RecyclerView.Adapter<ListaRecyclerAdaptador.ItemViewHolder>`
   - 5.2 Classe interna **`ItemViewHolder extends RecyclerView.ViewHolder`** (gere/reutiliza linhas); o construtor recebe o binding da linha:
     ```java
     private final ItemListaLivroBinding binding;
     public ItemViewHolder(ItemListaLivroBinding binding) {
         super(binding.getRoot());
         this.binding = binding;
     }
     ```
   - 5.3 Os 3 métodos: `onCreateViewHolder()` (cria visualmente uma linha), `onBindViewHolder()` (preenche), `getItemCount()` (conta).
   - 5.4 `ArrayList<Livro> listaLivros` inicializado no construtor.
   - 5.5 `onCreateViewHolder` (só estrutura; os dados vêm depois):
     ```java
     ItemListaLivroBinding binding = ItemListaLivroBinding.inflate(
             LayoutInflater.from(parent.getContext()), parent, false);
     return new ItemViewHolder(binding);
     ```
   - 5.6 `onBindViewHolder` + `bind()` (em `ItemViewHolder`):
     ```java
     public void onBindViewHolder(@NonNull ItemViewHolder holder, int position) {
         Livro livro = listaLivros.get(position);
         holder.bind(livro);
     }
     public void bind(Livro livro) {
         binding.tvTitulo.setText(livro.getTitulo());
         // TODO: completar (autor, ano, capa...)
         binding.getRoot().setOnClickListener(v -> listener.onLivroClick(livro));   // 5.7.4
     }
     ```
   - 5.7 **Interface de clique** (o `ListaLivrosFragment` é informado do livro selecionado):
     - `interface OnLivroClickListener { void onLivroClick(Livro livro); }`
     - declarar o listener no adaptador; o **construtor** recebe `(lista, listener)`.
6. Em **`ListaLivrosFragment`**: criar o adaptador `(lista, listener)`; no `onCreateView()` (depois do inflate do binding, antes do `return`):
   ```java
   binding.rvLivros.setLayoutManager(new LinearLayoutManager(requireContext()));
   binding.rvLivros.setAdapter(adaptador);
   ```
   ⚠ **Sem `LayoutManager` a lista não aparece.** Implementar `onLivroClick()`; inicialmente mostrar um **Toast com o título**.
7. Em **`MenuMainActivity`**: no `onNavigationItemSelected()`, "Livros (Lista)" mostra `ListaLivrosFragment`; testar.
8. Clicar num item abre **`DetalhesLivroActivity`**:
   - 8.1 Layout com campos **editáveis** (`EditText`; o ano é do tipo **Number**).
   - 8.2 `onLivroClick()` inicia a atividade com a informação:
     - 8.2.1 `SingletonGestorLivros.getLivro(int id)`;
     - 8.2.2 **o ID** passa no bundle *Extra* do Intent (por chave);
     - 8.2.3 `DetalhesLivroActivity` usa o ID para carregar o livro;
     - 8.2.4 tema com **ActionBar** e título **"Detalhes: [título do livro]"**.
9. (*) Fragmento com os livros **em grelha** (2.ª opção): `RecyclerView` + **`GridLayoutManager`** + adaptador próprio; cada item mostra **apenas a capa**.

### Exemplo completo do adaptador (resolve a ficha)
```java
public class ListaRecyclerAdaptador extends RecyclerView.Adapter<ListaRecyclerAdaptador.ItemViewHolder> {

    public interface OnLivroClickListener { void onLivroClick(Livro livro); }

    private final ArrayList<Livro> listaLivros;
    private final OnLivroClickListener listener;

    public ListaRecyclerAdaptador(ArrayList<Livro> listaLivros, OnLivroClickListener listener) {
        this.listaLivros = listaLivros;
        this.listener = listener;
    }

    @NonNull @Override
    public ItemViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        ItemListaLivroBinding binding = ItemListaLivroBinding.inflate(
                LayoutInflater.from(parent.getContext()), parent, false);
        return new ItemViewHolder(binding);
    }

    @Override
    public void onBindViewHolder(@NonNull ItemViewHolder holder, int position) {
        holder.bind(listaLivros.get(position));
    }

    @Override public int getItemCount() { return listaLivros.size(); }

    class ItemViewHolder extends RecyclerView.ViewHolder {
        private final ItemListaLivroBinding binding;

        ItemViewHolder(ItemListaLivroBinding binding) {
            super(binding.getRoot());
            this.binding = binding;
        }

        void bind(Livro livro) {
            binding.tvTitulo.setText(livro.getTitulo());
            binding.getRoot().setOnClickListener(v -> listener.onLivroClick(livro));
        }
    }
}
```
> O exemplo é nosso (a ficha dá os passos e fragmentos de código). Para o **VolLink**: substituir `Livro` por `Evento`/`Candidatura` e `SingletonGestorLivros` por um singleton que chama a API REST (Volley).

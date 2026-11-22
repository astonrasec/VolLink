# Android 06 — Menus, Up Action, SharedPreferences e Widgets (+ Ficha 6 "Books")

> Fontes: `06_Menus_Preferences.pdf` (Cap. 6), `06_Books_Menus_Widgets.pdf` (Ficha 6).
> ⚠ O enunciado exige **SharedPreferences** (ficheiros locais) e um **menu de configuração onde se insere o endereço do servidor da API** — está tudo neste capítulo.

---

## 1. Menus

Componentes comuns da UI; apresentam **ações do utilizador e outras opções** em cada atividade. Três tipos:

| Tipo | Descrição |
|---|---|
| **Option Menu** (menu de opções) | O mais comum. ≤ 2.3: botão físico, até 6 itens (o 6.º "Mais"). Desde 3.0: **Action Bar**, canto superior direito, em *drop-down* ou forçado na barra |
| **Context Menu** (menu flutuante) | Aparece com **click longo** num componente; ações que afetam o conteúdo selecionado |
| **Popup Menu** | Implementação mais atual (3.0); menu **modal ancorado a uma View**, aparece sob a view (ou sobre ela se não houver espaço) |

### 1.1 Definição em XML (`res/menu/`)
Vantagens: estrutura mais fácil de visualizar, separa conteúdo do código, permite configurações alternativas (versão/ecrã) e reutilização.
Elementos:
- `<menu>` — contentor (contém `<item>` e `<group>`);
- `<item>` — um item (pode conter `<menu>` aninhado → submenu);
- `<group>` — contentor invisível para partilhar propriedades (estado ativo, visibilidade).

Atributos principais do `<item>`:
| Atributo | Função |
|---|---|
| `android:id` | Id único (reconhecer a seleção) |
| `android:icon` | Drawable do ícone |
| `android:title` | String do título |
| `app:showAsAction` | Se/como aparece como ação na Action Bar: `never`, `ifRoom`, `always`… |

Para usar o menu é preciso **fazer *inflate*** (`MenuInflater.inflate()`), consoante o tipo.

### 1.2 Options Menu
`menu_opcoes.xml`:
```xml
<?xml version="1.0" encoding="utf-8"?>
<menu xmlns:app="http://schemas.android.com/apk/res-auto"
      xmlns:android="http://schemas.android.com/apk/res/android">
    <item android:id="@+id/menu_1"
          android:icon="@android:drawable/ic_menu_sort_alphabetically"
          android:title="@string/txtItemMenu1"
          app:showAsAction="always" />
    <item android:id="@+id/menu_2"
          android:icon="@android:drawable/ic_input_add"
          android:title="@string/txtItemMenu2"
          app:showAsAction="always" />
    <item android:id="@+id/menu_3"
          android:icon="@android:drawable/ic_search_category_default"
          android:title="@string/txtItemMenu3"
          app:showAsAction="always" />
</menu>
```
Associar à atividade (`onCreateOptionsMenu`):
```java
@Override
public boolean onCreateOptionsMenu(Menu menu) {
    MenuInflater inflater = getMenuInflater();
    inflater.inflate(R.menu.menu_opcoes, menu);
    return super.onCreateOptionsMenu(menu);
}
```
Para a **Action Bar** aparecer, o tema em `themes.xml` tem de ter ActionBar (não `NoActionBar`) e o layout principal precisa de `android:layout_marginTop="?attr/actionBarSize"` para não sobrepor:
```xml
<resources xmlns:tools="http://schemas.android.com/tools">
    <style name="Base.Theme.GestaoContactos" parent="Theme.Material3.DayNight">
        <item name="colorPrimary">@color/purple_500</item>
    </style>
    <style name="Theme.GestaoContactos" parent="Base.Theme.GestaoContactos" />
</resources>
```
Processar a seleção — `onOptionsItemSelected()` (inclui itens de ação da Action Bar); devolve **`true`** quando tratou; senão chama o `super` (devolve `false`):
```java
@Override
public boolean onOptionsItemSelected(MenuItem item) {
    if (item.getItemId() == R.id.menu_1) {
        Toast.makeText(this, item.getTitle(), Toast.LENGTH_SHORT).show();
        return true;
    } else if (item.getItemId() == R.id.menu_2) { /* ... */ return true; }
    else if (item.getItemId() == R.id.menu_3) { /* ... */ return true; }
    return super.onOptionsItemSelected(item);
}
```

### 1.3 Context Menu
1. `registerForContextMenu(view)` — registar a view associada;
2. implementar `onCreateContextMenu()` e fazer *inflate* do menu;
3. tratar o item em `onContextItemSelected()`.

### 1.4 Popup Menu
1. Implementar o `onClick` da View-âncora;
2. instanciar `PopupMenu(context, viewÂncora)`; *inflate* com `MenuInflater`; `PopupMenu.show()`;
3. a atividade implementa **`PopupMenu.OnMenuItemClickListener`**, regista com `setOnMenuItemClickListener()`; o sistema chama `onMenuItemClick()`.

---

## 2. Up Action (botão "voltar atrás" na Action Bar)

- Cada app deve ter forma fácil de chegar à atividade principal → **botão Up** na Action Bar em **todas as atividades exceto a principal**; ao premir navega para a **atividade pai**.
- **Declarar a atividade pai no manifest:**
  - desde Android 4.1 (atributo recomendado):
    ```xml
    <activity android:name=".ListViewContactosActivity"
              android:parentActivityName=".MainActivity"/>
    ```
  - versões antigas (`<meta-data>`):
    ```xml
    <activity android:name=".ListViewContactosActivity">
        <meta-data android:name="android.support.PARENT_ACTIVITY"
                   android:value="dei.amsi.gestaocontactos.MainActivity"/>
    </activity>
    ```
- **Ativar** o botão Up (opcional) em `onCreate`:
  ```java
  if (getSupportActionBar() != null) {
      getSupportActionBar().setDisplayHomeAsUpEnabled(true);
  }
  ```

---

## 3. SharedPreferences (persistência de dados simples)

**Necessidade:** persistir dados durante/após o uso da app (preferências do utilizador) — dados simples/isolados que **não justificam BD**.

**Exemplos de uso:** flag "primeira vez que o utilizador entra"; dados de login; preferências (ex.: unidade de temperatura).
> ⛔ **NUNCA guardar passwords ou dados pessoais/sensíveis** (telefone, etc.) em SharedPreferences.

### 3.1 Conceitos
- Interface para ler/alterar preferências; formato **chave → valor**; tipos: `int`, `float`, `boolean`, `String`, `Set<String>` (e `long`).
- Duas formas:
  - **`getPreferences(mode)`** — ficheiro associado à Activity, sem indicar nome;
  - **`getSharedPreferences(name, mode)`** — ficheiros **identificados por nome**, acessíveis por várias Activities (criar um por categoria de dados).
- Modo de acesso: **`MODE_PRIVATE`** (só a app) — `MODE_APPEND` aplica-se à criação de ficheiros.
- Alterar: `SharedPreferences.Editor` (`sharedPreferences.edit()`) → `putXxx(chave, valor)` → **`commit()`** (síncrono, desde a 1.ª API) ou **`apply()`** (assíncrono, desde a versão 9).

### 3.2 Exemplo do slide
```java
public class MainActivity extends AppCompatActivity {
    private String nome;
    SharedPreferences sharedPreferences;
    SharedPreferences.Editor editor;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        sharedPreferences = getPreferences(Context.MODE_PRIVATE);   // modo de acesso
        editor = sharedPreferences.edit();                          // Editor
        nome = sharedPreferences.getString("meuNome", "");          // chave + valor por omissão

        if (nome.isEmpty()) {     // o slide usa nome == "" (comparar Strings deve ser com equals/isEmpty)
            Toast.makeText(this, "Nome não estava guardado", Toast.LENGTH_SHORT).show();
            editor.putString("meuNome", "Sónia");
            editor.apply();
        } else {
            Toast.makeText(this, "Nome: " + nome, Toast.LENGTH_SHORT).show();
        }
    }
}
```

### 3.3 Padrão para as definições do VolLink (nosso)
```java
public class AppPrefs {
    private static final String FICHEIRO = "vollink_prefs";
    public static final String KEY_SERVER = "api_base_url";

    public static String getServidor(Context c) {
        return c.getSharedPreferences(FICHEIRO, Context.MODE_PRIVATE)
                .getString(KEY_SERVER, "http://10.0.2.2/vollink/backend/web/api");  // 10.0.2.2 = localhost do PC no emulador
    }
    public static void setServidor(Context c, String url) {
        c.getSharedPreferences(FICHEIRO, Context.MODE_PRIVATE).edit().putString(KEY_SERVER, url).apply();
    }
}
```

---

## 4. Desafio do capítulo
Menu de opções na `MainActivity` da gestão de contactos: (1) `menu.xml` com 3 itens; (2) associar à atividade principal; (3) tratar a seleção; (4) **comparar** o efeito de `showAsAction` (`never` / `ifRoom` / `always`).

Próximo tema: **Estilos e Temas**, **FloatingActionButton** (`07-estilos-temas.md`).

---

## 5. Ficha 6 — *Books*: Menus e Widgets

**Objetivos:** criar/manipular menus; criar/manipular **FloatingActionButton**; usar widgets para manipular/apresentar informação.

1. Em `MenuMainActivity`, usar **SharedPreferences** para guardar o **email** do utilizador.
2. Adicionar **Image Asset** (botão direito em `drawable`) para ícones de **Pesquisar, Adicionar, Remover, Guardar**. Se o Clip Art não tiver, descarregar em <https://fonts.google.com/icons> em **XML** e *New → Image Asset → Image*.
3. **FloatingActionButton** em `ListaLivrosFragment` e `GrelhaLivrosFragment`:
   ```xml
   <com.google.android.material.floatingactionbutton.FloatingActionButton
       android:id="@+id/fabLista"
       android:layout_width="wrap_content" android:layout_height="wrap_content"
       android:layout_gravity="bottom|end" android:layout_margin="16dp"
       android:backgroundTint="@color/colorPrimary" app:tint="@color/white"
       android:focusable="true" android:src="@drawable/ic_action_adicionar" />
   ```
   3.2 No `onViewCreated`, `setOnClickListener` → abrir **`DetalhesLivroActivity`** (para **criar** novo livro).
4. **`menu_pesquisa.xml`** (botão direito em `res/menu` → *Menu Resource File*) com `itemPesquisa`:
   ```xml
   <item android:id="@+id/itemPesquisa"
         android:icon="@drawable/ic_action_pesquisar"
         android:title="Pesquisa"
         app:actionViewClass="android.widget.SearchView"
         app:showAsAction="always"/>
   ```
5. **Menu de opções no fragmento** `ListaLivrosFragment`:
   - 5.1 `setHasOptionsMenu(true);` em `onCreateView` (necessário para o fragmento mostrar menu);
   - 5.2 reimplementar `onCreateOptionsMenu()` para inflar `menu_pesquisa.xml`;
   - 5.3 atributo `SearchView`;
   - 5.4 pesquisa **em runtime** com `setOnQueryTextListener(...)`;
   - 5.5 `onQueryTextChange()` **filtra os livros** (lista atualizada à medida que se escreve).
   - ⚠ `setHasOptionsMenu` está **deprecated** (substituído por `MenuProvider`), mas a ficha usa-o.
6. **`menu_remover.xml`** com `itemRemover`, associado a **`DetalhesLivroActivity`** (reimplementar `onCreateOptionsMenu()`).
7. Em `Livro`: **`id` incremental e único** — o construtor deixa de receber `id`; ajustar `gerarLivros()` no singleton.
   ```java
   private static int nextId = 1;
   private final int id;
   public Livro(int capa, int ano, String titulo, String autor, String serie) {
       this.id = nextId++;
       ...
   }
   ```
8. **`SingletonGestorLivros`**: `adicionarLivro(Livro)`, `editarLivro(Livro)`, `removerLivro(int idLivro)` (operam na lista em memória).
9. **`DetalhesLivroActivity`** com FAB para **criar/guardar**:
   - sem id no *Extra* → **adicionar** livro novo; com id → **editar**;
   - 9.1 o ícone do FAB adapta-se (adicionar vs gravar); **validar que todos os campos estão preenchidos**;
   - 9.2 ao terminar a inserção/edição, a lista/grelha é **atualizada** e aparece um **Toast** com a informação;
   - 9.3 trocar `startActivity` por **`startActivityForResult`** (com código de ação) e reimplementar `onActivityResult()`.
10. **Remover livro** em `DetalhesLivroActivity`:
    - 10.1 com id recebido, o menu `menu_remover.xml` fica disponível;
    - 10.2 `onOptionsItemSelected()` deteta o item;
    - 10.3 usar **`AlertDialog`** para **confirmar** a remoção.
12. (*) **Investigar:** `SwipeRefreshLayout` (sincronizar dados da lista/grelha) e substituir **Toast** por **Snackbar**.

### Esboço do AlertDialog de confirmação (nosso)
```java
new AlertDialog.Builder(this)
    .setTitle("Remover livro")
    .setMessage("Tem a certeza que pretende remover este livro?")
    .setPositiveButton("Sim", (d, w) -> {
        SingletonGestorLivros.getInstance().removerLivro(idLivro);
        setResult(RESULT_OK);
        finish();
    })
    .setNegativeButton("Não", null)
    .show();
```

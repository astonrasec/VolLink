# Android 04 — Fragmentos, DialogFragment e NavigationView (+ Ficha 4 "Books")

> Fontes: `04_Fragmentos_NavigationView.pdf` (Cap. 4), `04_Books_NavDrawer_Fragmentos.pdf` (Ficha 4).
> ⚠ O enunciado do projeto exige **fragmentos** em algumas funcionalidades — este capítulo é diretamente avaliado.
> Nos slides há vários erros de digitação (`beginTrasaction`, `FragmentTransacttion`, aspas tipográficas…); abaixo o código está corrigido.

---

## 1. Fragmentos — conceito

- Um **fragmento** é uma **classe reutilizável** que implementa uma **porção de uma atividade** (tipicamente parte da interface).
- Têm de ser **incorporados em atividades**; **não correm de forma independente**, mas são objetos independentes da atividade e podem ser usados por várias atividades.
- Têm **ciclo de vida próprio, sempre dependente do da atividade**.

### 1.1 Ciclo de vida — criação
| Método | Quando |
|---|---|
| `onAttach(Activity)` | Após o fragmento se associar à atividade |
| `onCreate(Bundle)` | Após a criação (ideal para inicializar componentes) |
| `onCreateView(LayoutInflater, ViewGroup, Bundle)` | Quando a UI é desenhada; **devolve a `View`** (inflate do layout) |
| `onViewCreated(View, Bundle)` | Logo após desenhar a UI (restaurar estado adicional da UI) |
| `onStart()` | Fragmento fica visível |
| `onResume()` | Antes de o utilizador poder interagir |

### 1.2 Ciclo de vida — destruição
| Método | Quando |
|---|---|
| `onPause()` | Utilizador já não interage (tratar da persistência de dados) |
| `onStop()` | Já não visível |
| `onSaveInstanceState(Bundle)` | Logo após `onStop` (guardar estado atual) |
| `onDestroyView()` | A View é destruída (libertar recursos) |
| `onDestroy()` | Já não está em uso (libertar recursos) |
| `onDetach()` | Deixa de estar associado à atividade |

---

## 2. Incorporar fragmentos numa Activity

A atividade deve estender `FragmentActivity` ou **`AppCompatActivity`** (esta dá suporte a todas as versões).

### 2.1 Estática (declarado no layout)
```xml
<fragment
    android:layout_width="match_parent" android:layout_height="match_parent"
    android:name="com.fragmentosapp.MyFragment"
    android:id="@+id/list" />

<!-- forma atual -->
<androidx.fragment.app.FragmentContainerView
    xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent" android:layout_height="match_parent"
    android:name="com.fragmentosapp.MyFragment"
    android:id="@+id/list" />
```

### 2.2 Dinâmica (em tempo de execução)
1. No layout da atividade, um `ViewGroup` (`FrameLayout` ou `FragmentContainerView`) para acolher o fragmento:
```xml
<LinearLayout ...>
    <FrameLayout android:id="@+id/placeholder" ... />
</LinearLayout>
<!-- ou -->
<LinearLayout ...>
    <androidx.fragment.app.FragmentContainerView
        android:layout_width="match_parent" android:layout_height="match_parent"
        android:id="@+id/placeholder" />
</LinearLayout>
```
2. Na atividade: **add / replace / remove** através do `FragmentManager` e de uma `FragmentTransaction`:
```java
FragmentManager fragmentManager = getSupportFragmentManager();
FragmentTransaction ft = fragmentManager.beginTransaction();
ft.add(R.id.placeholder, new MyFragment());   // ou replace / remove
ft.commit();                                   // aplica as alterações
```

---

## 3. Aceder a instâncias (Activity ↔ Fragment)

- **Fragment → Activity:** `getActivity()`
  ```java
  View listview = getActivity().findViewById(R.id.list);
  ```
- **Activity → Fragment:** via `FragmentManager`
  - estático (definido no layout): `findFragmentById(id)`
    ```java
    DemoFragment fragmentDemo = (DemoFragment)
        getSupportFragmentManager().findFragmentById(R.id.fragmentDemo);
    ```
  - dinâmico: `findFragmentByTag(tag)` (a tag é passada no `replace/add`)
    ```java
    getSupportFragmentManager().beginTransaction()
        .replace(R.id.container, new DemoFragment(), "MY_TAG").commit();
    DemoFragment f = (DemoFragment) getSupportFragmentManager().findFragmentByTag("MY_TAG");
    ```

---

## 4. Comunicação

Os fragmentos **não comunicam diretamente entre si** — apenas através da **Activity** (ou de recursos que ela oferece).

### 4.1 Activity → Fragment por **Bundle** (factory `newInstance`)
Passos: método estático que (1) recebe dados, (2) cria o fragmento, (3) cria `Bundle`, (4) mete os args, (5) `setArguments`, (6) devolve; o fragmento lê com `getArguments()` em `onCreate()`; a atividade carrega o fragmento com esse método.
```java
public class DemoFragment extends Fragment {
    @Override
    public void onCreate(Bundle b) {
        super.onCreate(b);
        int myint = getArguments().getInt("someInt", 0);
        String myString = getArguments().getString("someString", "");
    }

    public static DemoFragment newInstance(int myInt, String myString) {
        DemoFragment fragmentDemo = new DemoFragment();
        Bundle args = new Bundle();
        args.putInt("someInt", myInt);
        args.putString("someString", myString);
        fragmentDemo.setArguments(args);
        return fragmentDemo;
    }
}

// Activity
FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
DemoFragment fragmentDemo = DemoFragment.newInstance(3, "Hello fragment!");
ft.add(R.id.placeholder, fragmentDemo);
ft.commit();
```
**Alternativa** — o Bundle como argumento da própria transação:
```java
public class ExampleActivity extends AppCompatActivity {
    public ExampleActivity() { super(R.layout.example_activity); }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (savedInstanceState == null) {              // só na 1.ª criação
            Bundle bundle = new Bundle();
            bundle.putInt("some_int", 0);
            getSupportFragmentManager().beginTransaction()
                .setReorderingAllowed(true)
                .add(R.id.fragment_container_view, ExampleFragment.class, bundle)
                .commit();
        }
    }
}

class ExampleFragment extends Fragment {
    public ExampleFragment() { super(R.layout.example_fragment); }
    @Override
    public void onViewCreated(@NonNull View view, Bundle savedInstanceState) {
        int someInt = requireArguments().getInt("some_int");
    }
}
```

### 4.2 Activity → Fragment por **métodos**
1. Criar métodos na classe do fragmento; 2. obter a referência na atividade; 3. invocar.
```java
public class DemoFragment extends Fragment {
    public void doSomething(String param) { /* código */ }
}
// Activity
DemoFragment fragmentDemo = (DemoFragment)
    getSupportFragmentManager().findFragmentById(R.id.fragmentDemo);
fragmentDemo.doSomething("Hello fragment!");
```

### 4.3 Fragment ↔ Fragment — **Fragment Result API**
Emissor:
```java
button.setOnClickListener(v -> {
    Bundle result = new Bundle();
    result.putString("bundleKey", "result");
    getParentFragmentManager().setFragmentResult("requestKey", result);
});
```
Recetor (`onCreate`):
```java
getParentFragmentManager().setFragmentResultListener("requestKey", this,
    new FragmentResultListener() {
        @Override
        public void onFragmentResult(@NonNull String requestKey, @NonNull Bundle bundle) {
            String result = bundle.getString("bundleKey");
            // fazer algo com o resultado
        }
    });
```

### 4.4 Guardar o estado do fragmento
```java
@Override
public void onSaveInstanceState(@NonNull Bundle outState) {
    super.onSaveInstanceState(outState);
    outState.putBoolean(IS_EDITING_KEY, isEditing);
    outState.putString(RANDOM_GOOD_DEED_KEY, randomGoodDeed);
}

@Override
public void onCreate(@Nullable Bundle savedInstanceState) {
    super.onCreate(savedInstanceState);
    if (savedInstanceState != null) {
        isEditing = savedInstanceState.getBoolean(IS_EDITING_KEY, false);
        randomGoodDeed = savedInstanceState.getString(RANDOM_GOOD_DEED_KEY);
    } else {
        randomGoodDeed = viewModel.generateRandomGoodDeed();
    }
}
```
A recuperação pode fazer-se em `onCreate`, `onCreateView` ou `onViewCreated`.

---

## 5. DialogFragment

- Fragmento específico que **se sobrepõe à Activity** como **janela modal**; usado para caixas de diálogo personalizadas. A classe herda de **`DialogFragment`**.
- Construção: (1) layout do diálogo; (2) classe que herda `DialogFragment`; (3) reescrever `onCreateView()` — inflate do layout, obter referências, definir listeners, `setCancelable(false)` para modal, e `dismiss()` para fechar.

`my_dialog_fragment.xml`:
```xml
<LinearLayout ... android:orientation="vertical">
    <TextView ... android:text="Confirma?..."/>
    <LinearLayout ... android:orientation="horizontal">
        <Button ... android:text="Não" android:id="@+id/botao_nao" />
        <Button ... android:text="Sim" android:id="@+id/botao_sim" />
    </LinearLayout>
</LinearLayout>
```
`MyDialogFragment.java`:
```java
public class MyDialogFragment extends DialogFragment implements View.OnClickListener {
    @Override
    public View onCreateView(LayoutInflater li, ViewGroup vg, Bundle b) {
        View view = li.inflate(R.layout.my_dialog_fragment, null);
        Button btnNao = view.findViewById(R.id.botao_nao);
        Button btnSim = view.findViewById(R.id.botao_sim);
        btnNao.setOnClickListener(this);
        btnSim.setOnClickListener(this);
        setCancelable(false);
        return view;
    }

    @Override
    public void onClick(View view) {
        if (view.getId() == R.id.botao_sim)
            Toast.makeText(getActivity(), "yes clicked", Toast.LENGTH_SHORT).show();
        if (view.getId() == R.id.botao_nao)
            Toast.makeText(getActivity(), "no clicked", Toast.LENGTH_SHORT).show();
        dismiss();
    }
}
```
Mostrar:
```java
FragmentManager fm = getSupportFragmentManager();   // o slide usa getFragmentManager() (deprecated)
MyDialogFragment mdf = new MyDialogFragment();
mdf.show(fm, "TAG");
```

### 5.1 Comunicação DialogFragment → Activity (interface interna)
Lado do DialogFragment: interface interna + atributo que guarda a referência da atividade + `onCreate()` que faz o cast.
```java
public class MyDialogFragment extends DialogFragment implements View.OnClickListener {
    private Communicator communicator;

    @Override
    public void onCreate(Bundle b) {
        super.onCreate(b);
        communicator = (Communicator) getActivity();
    }
    // ... onCreateView / onClick ...

    interface Communicator {
        void onDialogMessage(String message);
    }
}
```
Lado da Activity: implementa a interface e reescreve o método.
```java
public class MainActivity extends AppCompatActivity implements MyDialogFragment.Communicator {
    private TextView txtResponse;

    @Override protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        txtResponse = findViewById(R.id.response);
    }
    public void callDialog(View view) {
        new MyDialogFragment().show(getSupportFragmentManager(), "Mydialog");
    }
    @Override public void onDialogMessage(String message) { txtResponse.setText(message); }
}
```

---

## 6. Navigation Drawer (`NavigationView` + `DrawerLayout`)

- Padrão muito usado; a *Design Support Library* simplificou-o com o **`NavigationView`**, incluído num **`DrawerLayout`**, que mostra itens a partir de um **ficheiro de menu**.
- O Android Studio tem *templates* de Navigation Drawer. A `NavigationView` deve usar-se com uma **`Toolbar`**.

### 6.1 Passos de implementação
1. **Menu** (`res/menu/drawer_menu.xml`):
```xml
<menu xmlns:android="http://schemas.android.com/apk/res/android"
      xmlns:tools="http://schemas.android.com/tools"
      tools:showIn="navigation_view">
    <group android:checkableBehavior="single">
        <item android:id="@+id/nav_lista"
              android:icon="@drawable/ic_action_lista"
              android:title="@string/txtItemLista" />
        <item ... />
    </group>
</menu>
```
2. **Cabeçalho** (`nav_header_main.xml`): `LinearLayout` vertical com fundo e `ImageView` + `TextView`s.
3. **Layout da Activity** → `DrawerLayout` contendo: (1) LinearLayout vertical, (2) `MaterialToolbar`, (3) `FrameLayout` (onde entra o fragmento), (4) `NavigationView`.
4. **Toolbar:** no manifest, `<application>` com tema **NoActionBar**, para não usar a `ActionBar` nativa:
```xml
<com.google.android.material.appbar.MaterialToolbar
    android:id="@+id/toolbar"
    android:layout_width="match_parent"
    android:layout_height="?attr/actionBarSize"
    android:background="?attr/colorPrimary"
    app:title="Página principal"
    app:titleTextColor="@android:color/white" />
```
```xml
<style name="Base.Theme.Books" parent="Theme.Material3.DayNight.NoActionBar"/>
<style name="Theme.Books" parent="Base.Theme.Books"/>
<style name="Theme.Books.WithActionBar" parent="Theme.MaterialComponents.DayNight.DarkActionBar">
    <item name="colorPrimary">@color/colorPrimaryDark</item>
</style>
```
5. **NavigationView** (referência a header e menu):
```xml
<com.google.android.material.navigation.NavigationView
    android:id="@+id/navigationView"
    android:layout_width="wrap_content"
    android:layout_height="match_parent"
    android:layout_gravity="start"
    android:fitsSystemWindows="true"
    app:headerLayout="@layout/nav_header_main"
    app:menu="@menu/drawer_menu" />
```
6. **`onCreate`:** definir a toolbar como barra da atividade (`setSupportActionBar(toolbar)` ou `setSupportActionBar(binding.toolbar)`).
7. **Hambúrguer** (`ActionBarDrawerToggle`) + listener + sincronização:
```java
ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(
        this, drawer, toolbar, R.string.open_drawer, R.string.close_drawer);
drawer.addDrawerListener(toggle);
toggle.syncState();
// com ViewBinding:
ActionBarDrawerToggle drawerToggle = new ActionBarDrawerToggle(this,
        binding.drawerLayout, binding.toolbar, R.string.menu_abrir, R.string.menu_fechar);
binding.drawerLayout.addDrawerListener(drawerToggle);
drawerToggle.syncState();
```
8. **Listener de cliques:** a atividade implementa `NavigationView.OnNavigationItemSelectedListener`:
```java
public class MainActivity extends AppCompatActivity
        implements NavigationView.OnNavigationItemSelectedListener {

    @Override protected void onCreate(Bundle savedInstanceState) {
        ...
        binding.navigationView.setNavigationItemSelectedListener(this);
    }

    @Override
    public boolean onNavigationItemSelected(MenuItem item) {
        if (item.getItemId() == R.id.drawer_menu_camera_id) {
            FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
            ft.replace(R.id.frame_layout, new CameraFragment());
            ft.addToBackStack(null);        // coloca na back stack
            ft.commit();
        } else if (item.getItemId() == R.id.drawer_menu_gallery_id) { ... }
        drawerLayout.closeDrawer(GravityCompat.START);
        return true;
    }
}
```

---

## 7. Ficha 4 — *Books*: Navegação e Fragmentos

**Objetivos:** adicionar menu de navegação ao *Books*; usar `NavigationView` e `Toolbar`; criar fragmentos.

1. Criar a atividade **`MenuMainActivity`** (Empty Views Activity), que **substitui a `MainActivity`**.
2. **Menu** — `res` → *New → Android Resource Directory* (tipo **menu**) → *New → Menu Resource File* `menu_main`.
   - Grupo com `android:checkableBehavior="single"` (itens partilham estado/visibilidade mas **não são clicáveis em simultâneo**).
   - IDs: **`navEstatico`**, **`navDinamico`**, **`navEmail`**.
   - 2.1 Para o menu aparecer no `NavigationView`: `tools:showIn="navigation_view"` na tag `<menu>`.
   - 2.2 Ícones: *New → Image Asset* → tipo **Action Bar and Tab Icons** → clipart.
3. **Cabeçalho** `nav_header_main` (aspeto na figura), `LinearLayout` 200dp, fundo `@color/colorPrimary`, `gravity="bottom"`, `padding=16dp`; **TODO** da ficha: `ImageView` + `TextView` (email).
4. **`activity_menu_main.xml`**: `ConstraintLayout` → **`DrawerLayout`** (`id=drawerLayout`, `fitsSystemWindows="true"`, `tools:openDrawer="start"`), contendo:
   - `MaterialToolbar` (id `toolbar`),
   - `FrameLayout` id **`fragmentContainer`**,
   - `NavigationView` id **`navigationView`** com `app:headerLayout="@layout/nav_header_main"` e `app:menu="@menu/menu_main"` (`layout_gravity="start"`, `fitsSystemWindows="false"`).
5. **Lógica Java:**
   - 5.1 `LoginActivity` passa a iniciar `MenuMainActivity`.
   - 5.2 Preencher o cabeçalho com o email recebido do Login.
   - 5.3 `carregarToolbar()`:
     ```java
     private void carregarToolbar() {
         setSupportActionBar(binding.toolbar);
         ActionBarDrawerToggle drawerToggle = new ActionBarDrawerToggle(this,
                 binding.drawerLayout, binding.toolbar, R.string.menu_abrir, R.string.menu_fechar);
         binding.drawerLayout.addDrawerListener(drawerToggle);
         drawerToggle.syncState();
         drawerToggle.getDrawerArrowDrawable().setColor(
                 ContextCompat.getColor(this, android.R.color.white));
     }
     ```
   - 5.4 Listener `OnNavigationItemSelectedListener`: ao selecionar → atualizar **título da Toolbar**; mostrar **mensagem (consola/Toast)**; **marcar o item**; **fechar o drawer**:
     ```java
     binding.navigationView.setNavigationItemSelectedListener(new NavigationView.OnNavigationItemSelectedListener() {
         @Override public boolean onNavigationItemSelected(MenuItem menuItem) {
             return selecionarItemMenu(menuItem);
         }
     });

     private boolean selecionarItemMenu(MenuItem menuItem) {
         int id = menuItem.getItemId();
         if (id == R.id.navEstatico) {
             // TODO: título + consola
         } else
             // TODO: navDinamico e navEmail
         menuItem.setChecked(true);
         binding.drawerLayout.closeDrawer(GravityCompat.START);
         return true;
     }
     ```
   - 5.5 Testar.
6. **Fragmentos:** criar **`EstaticoFragment`** e **`DinamicoFragment`** (*Fragment (Blank)*), copiando o conteúdo dos layouts `activity_estatico.xml`/`activity_dinamico.xml` para `fragment_estatico.xml`/`fragment_dinamico.xml`. Do código gerado só ficam **construtor** e **`onCreateView`**.
   - Estático (findViewById):
     ```java
     public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
         View view = inflater.inflate(R.layout.fragment_estatico, container, false);
         TextView tvTitulo = view.findViewById(R.id.tvTitulo);
         tvTitulo.setText(/* ... */);
         return view;
     }
     ```
   - Dinâmico (**ViewBinding**):
     ```java
     public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
         binding = FragmentDinamicoBinding.inflate(inflater, container, false);
         binding.tvTitulo.setText(/* ... */);
         return binding.getRoot();
     }
     ```
7. **`FragmentManager`** — método `carregarFragmento()` e usar no listener:
   ```java
   private void carregarFragmento(Fragment fragment) {
       getSupportFragmentManager()
           .beginTransaction()
           .setReorderingAllowed(true)
           .replace(R.id.fragmentContainer, fragment)
           .commit();
   }
   ```
8. Inicialmente nenhum item está selecionado → fazer com que ao abrir `MenuMainActivity` o item **"Livro Estático"** esteja selecionado (e o fragmento carregado).
9. Testar; apagar as atividades antigas (**`MainActivity`**, **`DetalhesEstaticoActivity`**, **`DetalhesDinamicoActivity`**) e respetivos layouts/dependências.

> **Para o VolLink:** este padrão (uma `Activity` com `DrawerLayout` + `FrameLayout` e vários fragmentos, p.ex. Eventos, Candidaturas, Perfil, Definições) cumpre o requisito "algumas funcionalidades têm de ser feitas com fragmentos".

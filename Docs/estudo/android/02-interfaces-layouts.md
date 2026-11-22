# Android 02 — Desenho de Interfaces Gráficas e Layouts

> Fontes: `02_Desenho_Interfaces_Graficas_Layouts.pdf` (Cap. 2), `02_Guess_Number.pdf` (Ficha 2),
> `02_1_EuroMilhoes.pdf` (Ficha 2.1 extra), `02_2_Calculadora.pdf` (Ficha 2.2 extra).

---

## 1. Composição de um projeto Android Studio

| Elemento | Função |
|---|---|
| **`AndroidManifest.xml`** | Obrigatório. Descreve a app, identifica os componentes (Activity, Service, BroadcastReceiver…) e define **permissões** (`<permission>`, `<uses-permission>`…) — funcionalidades protegidas, interagir/aceder a outras apps |
| **`java/`** | Código `.java`: todas as atividades (uma é a **principal**, executada no arranque). Contém também testes: pacote `test` (unitários) e `androidTest` (instrumentados) |
| **`res/drawable`** | Imagens (podem separar-se por densidade/resolução) |
| **`res/layout`** | Ficheiros `.xml` que definem os ecrãs (UI) |
| **`res/mipmap`** | Ícones da app |
| **`res/values`** | `strings.xml`, `colors.xml` (internacionalização), **`themes`** (tema/aparência, modo claro e escuro) |

---

## 2. ViewGroup e View

- A UI de uma app é uma **hierarquia** de objetos:
  - **ViewGroup (Layout)** — invisível; contém outros ViewGroup/View; só define a disposição (horizontal, vertical…).
  - **View (Widget)** — componente com funcionalidade e que permite interação (botões, campos de texto, listas…).
- Layout pode ser construído de forma **visual** (drag & drop) ou **declarativa** (XML). O XML separa melhor apresentação e comportamento.
- Views e ViewGroups também podem ser criados **em tempo de execução**.
- Ficheiros XML em `res/layout`, registados na classe **`R`**.

---

## 3. Associar o layout à atividade

### 3.1 Via classe `R` + `findViewById`
```java
public class AboutActivity extends AppCompatActivity {
    private TextView textViewNome;
    private EditText editTextEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_about);          // atribui o layout

        textViewNome  = findViewById(R.id.textViewNome);
        editTextEmail = findViewById(R.id.etEmail);

        textViewNome.setText("Sónia Luz");
        editTextEmail.setText("sonia.luz@ipleiria.pt");
    }
}
```
- `setContentView()` é invocado dentro de `onCreate()`, com a referência única em `R`.
- **Não há relação direta entre o nome do ficheiro de layout e o nome da atividade.**

### 3.2 Via **View Binding** (recomendado)
- Disponível desde Android Studio **3.6**; gera uma classe de vinculação por layout (`activity_about.xml` → `ActivityAboutBinding`); substitui `findViewById()` e evita erros de acesso.
- Ativar em `build.gradle.kts (Module: app)` e fazer *Sync Now*:
```kotlin
android {
    ...
    buildFeatures {
        viewBinding = true
    }
}
```
- Uso:
```java
public class AboutActivity extends AppCompatActivity {
    private ActivityAboutBinding binding;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityAboutBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());
        binding.textViewNome.setText("Sónia Luz");
        binding.editTextEmail.setText("sonia.luz@ipleiria.pt");
    }
}
```
Passos: atributo `binding` → `inflate(getLayoutInflater())` → `setContentView(binding.getRoot())` → aceder às views via `binding.<id>`.

---

## 4. Parâmetros de layout

- Atributos `layout_something` definem parâmetros da View **de acordo com o layout (pai) onde está**; cada ViewGroup define os parâmetros das suas *child views*.
- Largura/altura (`layout_width`, `layout_height`):
  - `wrap_content` — ajusta ao conteúdo
  - `match_parent` — preenche o máximo permitido pelo pai
  - valor exato — possível, **não recomendado**
  - `0dp` — significado depende do layout (ex.: com `layout_weight` no LinearLayout, ou "match constraints" no ConstraintLayout)
- Unidades: `px`, **`dp`** (density-independent), **`sp`** (scaled pixels, para texto), `in`, `mm`.

---

## 5. Tipos de layouts

Lista do slide: `LinearLayout`, `RelativeLayout`, `ConstraintLayout`, `TableLayout`, `GridLayout`, `CoordinatorLayout`, …  
Mais comuns: **Linear** (uma linha horizontal ou vertical; faz scroll se exceder o ecrã), **Relativo** (posições relativas entre filhos e ao pai) e **WebView** (renderiza páginas web dentro da app).

| Layout | Características-chave |
|---|---|
| **LinearLayout** | Filhos em sequência vertical/horizontal. `android:orientation`; alinhamento `android:layout_gravity`; pesos `android:layout_weight` para ocupar espaço restante; respeita margens |
| **RelativeLayout** | Posição relativa a outros componentes (ou ao pai). Relações criadas automaticamente pelo IDE pela ordem/local de inserção. Atributos: `layout_below`, `layout_toRightOf`/`layout_toEndOf`, `layout_alignEnd` |
| **ConstraintLayout** | Layouts grandes e complexos com hierarquia **flat** (sem sub-layouts); todas as views se relacionam entre si e com o pai; ideal para o Layout Editor (drag & drop) |
| **TableLayout** | Linhas (`TableRow`) e colunas; cada célula só contém uma view. Sem width/height declarados: largura=`match_parent`, altura=`wrap_content` |
| **GridLayout** | Linhas e colunas; elementos distribuídos pelas células; orientação horizontal ou vertical determina a sequência |
| **CoordinatorLayout** | Introduzido na Design Support Library 24.1; coordena as views filhas via **Behaviors** e âncoras; usado com `AppBarLayout`, `Snackbar`, `FloatingActionButton` |

---

## 6. Ficha 2 — *Guess a Number*

**Objetivos:** criar o projeto; usar `TextView`, `EditText`, `Button`; perceber as restrições dos layouts.

### Passo a passo
1. Novo projeto → *Empty Views Activity*, Java, API 27 (nome/caminho sem espaços).
2. Em `activity_main.xml`: mudar **ConstraintLayout → LinearLayout**; apagar o "Hello World"; adicionar uma caixa de texto e um botão.
   - Caixa de texto: **Edit Text Number**, ID **`etNumero`** (só aceita números).
   - Botão: ID **`btnAdivinha`**.
   - Strings no `strings.xml` (regra de arquitetura): recurso `txt_btn_adivinha` = "Adivinha" (propriedade *Text* → botão "…" → `+` → *String value*).
   - `onClick` via XML: propriedade `onClick` = `onClickAdivinha`; `Alt+Enter` → *Create onClick event handler*.
   - ⚠ *Nota da ficha:* este modo está **deprecated**; a forma recomendada é com **listener** (aulas seguintes).
3. Em `MainActivity.java`:
   - 3.1 obter o campo com `findViewById()` em `onCreate()` (View Binding é a alternativa mais segura);
   - 3.2 mostrar o valor com um **`Toast`** em `onClickAdivinha`;
   - 3.3 método que gera um inteiro aleatório entre dois valores recebidos por parâmetro;
   - 3.4 atributo que guarda o valor gerado para comparar;
   - 3.5 `onClickAdivinha` verifica se acertou.
4. Substituir o `Toast` por um **`TextView`** com o resultado (abaixo / acima / acertou).
5. **Limitar tentativas** (ex.: 5): método `iniciarJogo()` chamado quando as tentativas se esgotam ou o utilizador acerta.

### Esboço de solução
```java
public class MainActivity extends AppCompatActivity {
    private static final int MAX_TENTATIVAS = 5;
    private EditText etNumero;
    private TextView tvResultado;
    private int numeroGerado, tentativas;

    @Override
    protected void onCreate(Bundle b) {
        super.onCreate(b);
        setContentView(R.layout.activity_main);
        etNumero = findViewById(R.id.etNumero);
        tvResultado = findViewById(R.id.tvResultado);
        iniciarJogo();
    }

    private int gerarAleatorio(int min, int max) {
        return new java.util.Random().nextInt(max - min + 1) + min;
    }

    private void iniciarJogo() { numeroGerado = gerarAleatorio(1, 100); tentativas = 0; }

    public void onClickAdivinha(View v) {
        int palpite = Integer.parseInt(etNumero.getText().toString());
        tentativas++;
        if (palpite == numeroGerado) { tvResultado.setText("Acertou!"); iniciarJogo(); }
        else if (tentativas >= MAX_TENTATIVAS) { tvResultado.setText("Perdeu! Era " + numeroGerado); iniciarJogo(); }
        else tvResultado.setText(palpite < numeroGerado ? "O número é maior" : "O número é menor");
    }
}
```

---

## 7. Ficha 2.1 (extra) — EuroMilhões

- Projeto **`Euromilhoes`**. Chave = **5 números (1–50)** + **2 estrelas (1–12)**.
- Layout com campos para o utilizador inserir 5 números e 2 estrelas.
- Botão que gera aleatoriamente a chave do sorteio e mostra a comparação: **quantos números e quantas estrelas** coincidem.
- Pontos de atenção: números gerados **sem repetição**; validar intervalos.

## 8. Ficha 2.2 (extra) — Calculadora simples

- Projeto **`Calculadora`**, operações sobre inteiros, layout **`RelativeLayout`**.
- Dica do enunciado: adicionar os componentes **de cima para baixo, da esquerda para a direita**; se o IDE errar, editar o XML com `layout_below`, `layout_toEndOf`, `layout_alignEnd` (tabela completa em `RelativeLayout.LayoutParams`).
- *Questão da ficha:* "Como resolver o desafio de alinhar o botão **ENTER** de modo a ocupar o espaço de dois botões?" — a ficha **não dá a resposta**. Sugestão nossa (a validar): no `RelativeLayout`, alinhar o ENTER com o botão da coluna anterior (`layout_toEndOf` / `layout_alignEnd`) e definir a **largura em `dp` igual à soma** de dois botões (+ margens), ou fazê-lo ocupar o espaço entre dois âncoras (`layout_toEndOf` do 1.º e `layout_alignEnd` do 2.º). Alternativa noutro layout: `GridLayout` com `columnSpan=2` ou `LinearLayout` com `layout_weight`.
- Fluxo de uso: **1º** número 1 → **2º** operador → **3º** número 2 → **4º** Enter. Após Enter, o resultado passa a ser o novo número 1.
- A atividade implementa `View.OnClickListener` com `public void onClick(View v)` — um único método responde a todos os botões.
- Extras: botão **AC** (limpa tudo) e **Apagar** (último valor/operação).
- **Testar divisão por zero** (tratar a exceção/validar).

## 9. Próximo tema (indicado no fim do capítulo)
Activities e Intents — ver [`03-activities-intents.md`](03-activities-intents.md). Bibliografia: *Estrutura de um projeto Android Studio*, *Interface do Utilizador*, *ViewGroups (Layouts)* em developer.android.com.

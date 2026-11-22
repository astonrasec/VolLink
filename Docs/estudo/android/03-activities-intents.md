# Android 03 — Activities e Intents (+ Ficha 3 "Books")

> Fontes: `03_Activities_e_Intents.pdf` (Cap. 3), `03_Books_Login_Atividades_Intents_VersaoB.pdf` (Ficha 3),
> `imagens.zip` (recursos do projeto *Books*).
> Os esquemas do ciclo de vida são imagens nos slides; a tabela de callbacks da secção 3.3 é complemento nosso (documentação oficial), assinalado como tal.

---

## 1. Aplicação Android = conjunto de atividades ligadas

- Uma app é normalmente um **conjunto de atividades interligadas**.
- Pelo menos uma está declarada no **`AndroidManifest.xml`** como atividade **principal (main)**: é iniciada pelo *launcher*, apresentada ao premir o ícone e é o **ponto de entrada**.

## 2. Atividade

- **Componente** da app que fornece um **ecrã** com o qual o utilizador interage (telefonar, fotografar, enviar email, ver mapa…).
- Tipicamente um par **código Java + XML** de layout; tem acesso a uma janela que ocupa o ecrã e contém os itens de GUI.
- Para criar: subclasse de `Activity` ou de uma subclasse existente, como `AppCompatActivity`.

### 2.1 Pilha de atividades (*back stack*)
- Uma atividade pode iniciar outra (para ações diferentes / disponibilizar todas as funcionalidades).
- Cada nova atividade é apresentada e colocada (**push**) numa pilha **LIFO**, sobrepondo-se à anterior.
- Quando a atual termina (ou o utilizador sai), é retirada (**pop**) e a anterior fica visível.

## 3. Estados e ciclo de vida

### 3.1 Estados
| Estado | Significado |
|---|---|
| **Resumed** (running) | Em primeiro plano, com o foco do utilizador |
| **Paused** | Ainda visível, mas outra atividade tem o foco (parcialmente transparente ou não cobre o ecrã todo). Totalmente "viva", mas o sistema pode eliminá-la em memória extremamente baixa |
| **Stopped** | Completamente obscurecida (*background*). Mantida em memória mas não anexada ao gestor de janelas; já não visível; pode ser eliminada se faltar memória |

### 3.2 Três "tempos" do ciclo de vida
- **Tempo de vida** = entre `onCreate()` e `onDestroy()`.
- **Tempo de visibilidade** = entre `onStart()` e `onStop()`.
- **Tempo de foreground** = entre `onResume()` e `onPause()`.

### 3.3 Callbacks (complemento nosso — doc. oficial)
| Callback | Quando |
|---|---|
| `onCreate(Bundle)` | Criação; inicializar UI (`setContentView`), binding, listeners |
| `onStart()` | Torna-se visível |
| `onResume()` | Entra em foreground e interação |
| `onPause()` | Perde foco (gravar estado leve, parar animações) |
| `onStop()` | Deixa de estar visível |
| `onRestart()` | Volta de *stopped* para *started* |
| `onDestroy()` | Antes de ser destruída |

Sequência normal de arranque: `onCreate → onStart → onResume`. Ao sair: `onPause → onStop → onDestroy`.

## 4. Intents

- **Intent** = objeto que liga dois componentes distintos (atividade↔atividade, atividade↔serviço…); é uma **"intenção de fazer algo"**; usado normalmente para **iniciar outra atividade**.
- Podem ter **filtros** declarados no `AndroidManifest.xml` (por **ação**, **categoria** ou **tipo de dados**).

### 4.1 Iniciar uma atividade — 4 passos
1. (opcional) configurar `AndroidManifest.xml` — `intent-filter`
2. Criar um `Intent` indicando a atividade
3. (opcional) configurar os dados a passar
4. Chamar `startActivity(intent)`

### 4.2 Intent **explícito**
- Especifica o componente pelo **nome de classe totalmente qualificado**; usado dentro da própria app (nova atividade em resposta a uma ação, serviço de download em background…).
```java
Intent intent = new Intent(this, SignInActivity.class);   // this = Context ("pai" da atividade)
startActivity(intent);
```
> No slide aparece `SignInActvity.Class` (erros de digitação); o correto é `SignInActivity.class`.

### 4.3 Intent **implícito**
- Não nomeia o componente; declara uma **ação geral**, que pode ser tratada por outra app.
- A plataforma oferece intents comuns: alarmes, calendário, email, contactos, SMS, mapas, câmara… (<https://developer.android.com/guide/components/intents-common.html>).
```java
public void onClickAbrir(View view) {
    String url = binding.editTextURL.getText().toString();
    if (url.length() > 5) {
        Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
        startActivity(intent);
    }
}
```
**Verificar se existe app capaz** antes de iniciar (`resolveActivity` + `getPackageManager()`):
```java
Intent intent = new Intent(Intent.ACTION_SENDTO);
intent.setData(Uri.parse("mailto:" + email));
intent.putExtra(Intent.EXTRA_SUBJECT, "PSI - AMSI 2026/2027");
if (intent.resolveActivity(getPackageManager()) != null) {
    startActivity(intent);
}
```

### 4.4 Passar dados — *Bundle Extra*
- O *extra* é um dicionário **chave → valor**.
```java
intent.putExtra(SobreActivity.NUMERO_GUESS, numGuess);          // quem envia
int numGuess = getIntent().getIntExtra(NUMERO_GUESS, -1);       // quem recebe (valor por omissão = -1)
String resultado = getIntent().getStringExtra(RESULTADO);
```
Boa prática dos slides: definir a **chave como constante** (`public static final String NUMERO_GUESS = "NUMERO_GUESS";`) na atividade que recebe.

### 4.5 Atividade para obter um **resultado**
- Iniciar com `startActivityForResult(intent, requestCode)` e reimplementar `onActivityResult(requestCode, resultCode, data)`.
- A atividade chamada devolve com `setResult(RESULT_OK | RESULT_CANCELED)` + `finish()`.
```java
// MainActivity
private static final int JOGAR_NOVAMENTE = 1;

private void apresentarResultado(String resultado) {
    Intent intent = new Intent(this, ResultadoActivity.class);
    intent.putExtra(ResultadoActivity.RESULTADO, resultado);
    startActivityForResult(intent, JOGAR_NOVAMENTE);
}

@Override
protected void onActivityResult(int requestCode, int resultCode, Intent data) {
    if (requestCode == JOGAR_NOVAMENTE) {
        if (resultCode == RESULT_OK) resetGame();
        else finish();
    }
}

// ResultadoActivity
public void onClickOk(View v)     { setResult(RESULT_OK);       finish(); }
public void onClickCancel(View v) { setResult(RESULT_CANCELED); finish(); }
```
> ⚠ `startActivityForResult`/`onActivityResult` foram **marcados como deprecated** nas APIs recentes; a alternativa moderna é a **Activity Result API** (`registerForActivityResult`). Os slides e fichas usam a forma clássica — é a esperada nas avaliações.

## 5. Exercícios do capítulo (sobre o projeto *GuessNumber*)
1. Botão na `MainActivity` → nova **`SobreActivity`** com nome e email do autor (2 `TextView`):
   ```java
   public void onClickSobre(View view) {
       startActivity(new Intent(this, SobreActivity.class));
   }
   ```
2. Enviar à `SobreActivity` **quantas vezes acertou** (atributo `numGuess` + `putExtra`); a `SobreActivity` lê com `getIntExtra` e escreve "Acertou no valor: N vezes."
3. **`ResultadoActivity`** (botões *Ok* / *Cancel*) pergunta se quer reiniciar após acertar ou esgotar tentativas (usa `startActivityForResult`).

Próximo tema: Fragmentos e NavigationView (ver `04-fragmentos-navigationview.md`).

---

## 6. Ficha 3 — *Books*: atividades e intents

**Objetivos:** projeto **Books** (desenvolvido ao longo do semestre) com atividade inicial de **Login**; criar atividades a partir de outras; enviar email; criar uma classe **Singleton**.

> Recursos: `imagens.zip` → `imagens/logo_ulo.jpg`, `programarandroid1.jpg`, `programarandroid2.jpg` (colocar na pasta `drawable`).

### 6.1 Passos
1. **Projeto "Books"**: *Launch Activity* = *Empty Views Activity*; adicionar imagens a `drawable`; **renomear** `MainActivity → LoginActivity` e `activity_main → activity_login`.
2. **`activity_login`**: `ImageView` (logo ULO), 2 `EditText` (email, password), `Button` Login.
   - `isEmailValido()` — valida email.
   - `isPasswordValida()` — password com **mínimo 4 caracteres**.
   - `onClick` do botão = `onClickLogin`: valida email e password **usando ViewBinding**; apresenta resultado num `Toast`.
   - Ativar ViewBinding em `build.gradle.kts (Module: app)`:
     ```kotlin
     android {
         namespace = "com.example.teste"
         compileSdk { version = release(36) { minorApiLevel = 1 } }
         buildFeatures { viewBinding = true }
     }
     ```
3. **`MainActivity`** (nova, *Empty Views Activity*) com **3 botões e 2 TextView**. Em `LoginActivity`, em vez do Toast, iniciar `MainActivity` por **Intent**, passando o **email** como parâmetro para mostrar na 2.ª `TextView`.
4. **`EstaticoActivity`** e **`DinamicoActivity`** mostram informação de um livro.
   - **Estático:** dados escritos diretamente nas `TextView` em `onCreate`.
   - **Dinâmico:** dados vêm do **Singleton** (ponto 6).
   - Na `MainActivity`, botões "Livro Estático"/"Livro Dinâmico" iniciam cada atividade.
   - **ActionBar com botão back** → criar tema novo em `res/values/themes`:
     ```xml
     <style name="Theme.Books.WithActionBar"
            parent="Theme.MaterialComponents.DayNight.DarkActionBar">
         <item name="colorPrimary">@color/colorPrimary</item>
     </style>
     ```
     No `AndroidManifest.xml`:
     ```xml
     <activity
         android:name=".EstaticoActivity"
         android:exported="false"
         android:theme="@style/Theme.Books.WithActionBar"
         android:parentActivityName=".MainActivity"
         android:windowSoftInputMode="adjustResize" />
     ```
     (igual para `DinamicoActivity`). Tema global sem ActionBar:
     ```xml
     <style name="Base.Theme.Books" parent="Theme.MaterialComponents.DayNight.NoActionBar">
         <item name="colorPrimary">@color/colorPrimary</item>
     </style>
     ```
   - **Testar** o que acontece ao voltar à `MainActivity` (o email deixa de aparecer porque a atividade é recriada).
   - Corrigir com `launchMode="singleTop"` na `MainActivity`:
     ```xml
     <activity android:name=".MainActivity"
               android:launchMode="singleTop"
               android:exported="false" />
     ```
     → ao fazer *back*, o email continua visível (a instância existente é reutilizada).
5. **Package `modelo`** com a classe **`Livro`**: atributos `id`, `titulo`, `serie`, `autor`, `ano`, `capa` (**`id`, `ano` e `capa` são `int`** — a capa é uma referência de recurso). Construtor com todos os atributos + getters/setters **exceto `setId`**.
6. **`SingletonGestorLivros`** (no package `modelo`): construtor **thread-safe** garantindo **instância única**; contém **lista de livros** iniciada no construtor; método que devolve os livros.
7. Implementar **`DinamicoActivity`** com estas classes.
8. Em `MainActivity` implementar **todos os callbacks do ciclo de vida** com `Log`/`System.out` e observar a ordem; usar o **debugger** com breakpoints.
9. (*) Botão **Enviar Email** com **Intent implícito** (ver 4.3).

### 6.2 Padrão Singleton thread-safe (exemplo para o ponto 6)
```java
public class SingletonGestorLivros {
    private static SingletonGestorLivros instance = null;
    private ArrayList<Livro> livros;

    public static synchronized SingletonGestorLivros getInstance() {
        if (instance == null) instance = new SingletonGestorLivros();
        return instance;
    }

    private SingletonGestorLivros() {          // privado: ninguém cria outra instância
        livros = new ArrayList<>();
        gerarDadosDinamicos();
    }

    private void gerarDadosDinamicos() {
        livros.add(new Livro(1, "Programar Android - 1", "1ª Temporada", "Autor", 2019, R.drawable.programarandroid1));
        livros.add(new Livro(2, "Programar Android - 2", "1ª Temporada", "Autor", 2019, R.drawable.programarandroid2));
    }

    public ArrayList<Livro> getLivros() { return new ArrayList<>(livros); }
}
```
> O Singleton será o ponto central dos dados (e mais tarde do acesso à API/BD) — padrão a replicar no VolLink.

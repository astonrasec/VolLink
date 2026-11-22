# Android 07 — Personalização com Estilos e Temas (+ FloatingActionButton)

> Fonte: `07_Estilos_Temas.pdf` (Cap. 7). Não existe ficha prática própria deste capítulo; o desafio final está na secção 7.

---

## 1. Conceitos

- A UI de cada app pode ser personalizada: **componente a componente, por tipo de componente, por atividade ou para toda a app**.
- **Estilo** = conjunto de propriedades que definem a aparência/formato de uma **View** ou janela (altura, cor/tamanho da fonte, padding, cor de fundo…), definido num recurso XML **independente do layout**.
- **Tema** = **estilo aplicado a uma atividade ou à aplicação inteira** (a partir do `AndroidManifest.xml`), em vez de a uma View individual. Todas as Views da atividade/app usam as propriedades do tema.

## 2. Definir estilos

- Ficheiro `.xml` em **`res/values/`** (normalmente `styles.xml` ou `themes.xml`).
- Raiz `<resources>`; um `<style name="...">` por estilo (**`name` obrigatório**, identifica univocamente).
- Um `<item name="...">` por propriedade; o valor pode ser palavra-chave, cor hexadecimal, referência a outro recurso, etc.
- Normalmente baseiam-se no `AppTheme` (estilo base de toda a app).

```xml
<resources>
    <style name="AppTheme.Button" parent="@android:style/Widget.Button">
        <item name="android:layout_width">match_parent</item>
        <item name="android:layout_height">wrap_content</item>
        <item name="android:textSize">18sp</item>
        <item name="android:textStyle">bold</item>
        <item name="android:typeface">normal</item>
        <item name="android:textColor">@android:color/holo_blue_dark</item>
    </style>
</resources>
```

### 2.1 Aplicar um estilo (antes vs depois)
```xml
<!-- antes: atributos repetidos em cada botão -->
<Button android:id="@+id/btnListaContactos"
        android:layout_width="match_parent" android:layout_height="wrap_content"
        android:textSize="18sp" android:textStyle="bold" android:typeface="normal"
        android:onClick="onClickListaContactos" android:text="@string/btnLCText" />

<!-- depois: com style -->
<Button android:id="@+id/btnListaContactos"
        style="@style/AppTheme.Button"
        android:onClick="onClickListaContactos" android:text="@string/btnLCText" />
```

### 2.2 Herança de estilos (`parent`)
- Herdar de um estilo existente e só definir o que se altera.
- **Estilo criado por nós** — duas formas:
```xml
<!-- via atributo parent -->
<style name="MyButton" parent="AppTheme.Button">
    <item name="android:background">@android:color/background_light</item>
</style>
<Button android:id="@+id/btnGrelhaContactos" style="@style/MyButton" android:text="@string/btnGCText"/>

<!-- via nome com ponto (herança implícita) -->
<style name="AppTheme.Button.MyButton">
    <item name="android:background">@android:color/background_light</item>
</style>
<Button android:id="@+id/btnGrelhaContactos" style="@style/AppTheme.Button.MyButton" android:text="@string/btnGCText"/>
```
- **Estilo incorporado do Android** — herança **obrigatoriamente** pelo atributo `parent`:
```xml
<style name="MyTextView" parent="@android:style/TextAppearance">
    <item name="android:textColor">#FF0000</item>
    <item name="android:textSize">24dp</item>
</style>
<TextView android:id="@+id/textViewId" style="@style/MyTextView" />
```

## 3. Definir e aplicar temas

- Definir um tema é como um estilo, mas o `parent` refere-se a um **tema do Android**, com base em `Theme.AppCompat` (garante que a app corre independentemente da plataforma; faz parte das Android Support Libraries, permitindo a dispositivos antigos usar novidades do SDK).
- Num tema pode aplicar-se um estilo a **todos os componentes de um tipo** (botões, TextViews…), usando estilos nossos:
```xml
<style name="AppTheme.Light" parent="Theme.AppCompat.Light">
    <item name="android:typeface">serif</item>
    <item name="android:buttonStyle">@style/AppTheme.Button</item>
    <item name="android:textViewStyle">@style/MyTextView</item>
</style>
```
- **Aplicar** (manifest):
```xml
<!-- a uma atividade -->
<activity android:name=".DetalhesContacto" android:theme="@style/AppTheme.Light">
<!-- a toda a aplicação -->
<application android:theme="@style/AppTheme.Light">
```
  - O tema de uma atividade **sobrepõe-se** ao da aplicação; sem tema específico, a atividade usa o da aplicação.

### 3.1 Temas ajustados à versão do Android
- Versões novas têm temas adicionais (quando não se herda de `Theme.AppCompat`).
- Para os usar mantendo compatibilidade: tema personalizado + **seleção de recursos por versão**.
- Exemplo do slide: `LightTheme` baseado no tema claro padrão; o mesmo tema mas **holográfico** (Android 3.0 / API 11) num ficheiro **`res/values-v11`** com o holográfico como `parent` → a app alterna automaticamente.

## 4. Mudar o tema em tempo de execução (via SharedPreferences)

```java
public class TemasActivity extends AppCompatActivity
        implements SharedPreferences.OnSharedPreferenceChangeListener {

    public static final String PREFERENCE_THEME = "Pref_Theme";   // grupo de preferências
    private static final String TEMA = "ThemeName";                // chave: nome do tema
    private SharedPreferences preferences;
    private SharedPreferences.Editor editor;
    private String themeName;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        preferences = getSharedPreferences(PREFERENCE_THEME, MODE_PRIVATE);
        themeName = preferences.getString(TEMA, "AppTheme.Light");
        int themeId = getResources().getIdentifier(themeName, "style", getPackageName());
        setTheme(themeId);                                  // ANTES de instanciar qualquer view
        preferences.registerOnSharedPreferenceChangeListener(this);
        setContentView(R.layout.activity_temas);
    }

    public void onClickMudarTema(View view) {
        themeName = themeName.equals("AppTheme.Light") ? "AppTheme.Dark" : "AppTheme.Light";
        // (o slide usa themeName=="AppTheme.Light", comparação por referência — usar equals)
        editor = preferences.edit();
        editor.putString(TEMA, themeName);
        editor.apply();
    }

    @Override
    public void onSharedPreferenceChanged(SharedPreferences sharedPreferences, String key) {
        if (key.equals(TEMA)) {
            this.recreate();      // termina e reinicia a atividade → tema atualizado
        }
    }
}
```
Pontos-chave: `setTheme()` **antes** de `setContentView()`; a atividade regista-se como listener de alterações das preferências; `recreate()` aplica o novo tema; é preciso fazê-lo em todas as atividades que devam mudar.

## 5. FloatingActionButton (FAB)

- Destaca **uma única ação** principal da secção da app; **ícone circular flutuante** sobre a UI.
- Tamanhos: **default (normal)** e **mini** — atributo `fabSize` ou método `setSize()`.
- Deve representar ações **comuns e positivas**: **Criar, Favoritos, Partilhar, Navegar, Explorar**.
- Herda de `ImageView`: ícone via atributo `src` ou `setImageDrawable(Drawable)`.
- Cor de fundo por omissão = `colorAccent` do tema; em runtime: `setBackgroundTintList(ColorStateList)`.

```xml
<com.google.android.material.floatingactionbutton.FloatingActionButton
    android:id="@+id/fabAddTeste"
    android:layout_width="wrap_content" android:layout_height="wrap_content"
    android:layout_gravity="top|end"
    android:clickable="true" android:focusable="true"
    android:backgroundTint="#FF4081"
    android:src="@android:drawable/ic_input_add" />
```

## 6. Observações para a nossa app (VolLink)
- Criar `AppTheme` com cores da marca (a web usa *teal*) e estilos para botões/campos; suportar `DayNight`.
- Usar o **FAB** para ações como *"Candidatar-me"* ou *"Criar evento"* (empresa), seguindo o critério "ação positiva".
- O tema escuro/claro pode ficar nas **definições** (SharedPreferences) junto do endereço do servidor.

## 7. Desafio do capítulo
1. Estilo personalizado aplicado a **cada botão**.
2. **Tema personalizado** que herde do estilo anterior, aplicado a **uma atividade** e depois a **toda a aplicação**.
3. **FAB** que realce uma única operação numa atividade.

Próximo tema: Persistência de dados — armazenamento interno e SQLite (`08-…`, `09-…`).

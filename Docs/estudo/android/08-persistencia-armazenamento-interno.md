# Android 08 — Persistência de Dados: Serialização e Armazenamento Interno

> Fonte: `08_Persistencia_Dados_Armazenamento_Interno.pdf` (Cap. 8). Sem ficha própria no material.
> Contexto: na UC pede-se **SharedPreferences** (ficheiros locais) e **SQLite** (BD local) — ver [`06`](06-menus-sharedpreferences.md) e [`09`](09-singleton-e-sqlite.md). Este capítulo explica as alternativas e a serialização.

---

## 1. Opções de armazenamento no Android

A escolha depende de: dados **privados** da app ou acessíveis a outras apps/utilizadores; **espaço** necessário.

| Opção | Para quê |
|---|---|
| **SharedPreferences** | Dados primitivos privados em pares chave-valor |
| **Armazenamento interno** | Dados privados na memória do dispositivo |
| **Armazenamento externo** | Dados públicos no armazenamento externo partilhado |
| **Base de dados local — SQLite** | Dados estruturados numa BD privada |
| **Ligação de rede** | Dados na web, no próprio servidor |

## 2. Serialização

Quando **não existe o Singleton** (ou se pretende gravar em ficheiros internos), os dados que circulam (pesquisa com resultado, adição, atualização, passagem entre atividades) têm de ser **objetos serializáveis (`Serializable`)**.

- **Serialização** = traduzir o estado de um objeto para um formato que possa ser **gravado num ficheiro**, **enviado por rede** ou **passado entre atividades**, e reconstruído (desserializado) depois, no mesmo ou noutro contexto.
- Java dá serialização automática mas exige implementar **`java.io.Serializable`** (marca a classe como "ok para serializar").

### 2.1 Exemplo (gestão de contactos)
```java
import java.io.Serializable;

public class Contacto implements Serializable { ... }
public class GestorContactos implements Serializable { ... }
```

### 2.2 Adicionar contacto → devolver resultado à lista
Fluxo: `ListaContactosActivity` inicia `AdicionarContactoActivity` **para obter resultado**; esta devolve o novo contacto; a lista adiciona-o e atualiza a `ListView`.

```java
// ListaContactosActivity
public static final int REQUEST_CODE_ADICIONAR_CONTACTO = 1;      // código de verificação do resultado

public void onClickAdicionarContacto(View view) {                  // ligado ao FAB
    Intent intent = new Intent(this, AdicionarContactoActivity.class);
    startActivityForResult(intent, REQUEST_CODE_ADICIONAR_CONTACTO);
}

@Override
protected void onActivityResult(int requestCode, int resultCode, Intent data) {
    if (requestCode == REQUEST_CODE_ADICIONAR_CONTACTO) {
        if (resultCode == RESULT_OK) {
            Contacto contacto = (Contacto) data.getSerializableExtra(
                    AdicionarContactoActivity.NOVO_CONTACTO);
            gestorContactos.adicionarContacto(contacto);
            adaptador.notifyDataSetChanged();                       // notificar o adaptador
        }
    }
    super.onActivityResult(requestCode, resultCode, data);
}
```
```java
// AdicionarContactoActivity
public static final String NOVO_CONTACTO = "NOVO_CONTACTO";
private EditText editTextID, editTextNome;

public void onClickFabGuardarContacto(View view) {
    int id = Integer.parseInt(editTextID.getText().toString());
    String nome = editTextNome.getText().toString();

    Contacto novoContacto = new Contacto(id, nome);
    Intent returnIntent = new Intent();
    returnIntent.putExtra(NOVO_CONTACTO, novoContacto);            // objeto Serializable
    setResult(RESULT_OK, returnIntent);
    finish();
}
```
```java
// GestorContactos
public void adicionarContacto(Contacto contacto) { contactos.add(contacto); }
```

## 3. Estado de uma atividade — `onSaveInstanceState()`

- Quando o sistema **destrói uma atividade** para recuperar memória, o objeto é destruído e **não é possível retomar o estado intacto**; se o utilizador voltar, a atividade é **recriada**.
- Para preservar informação importante: reimplementar **`onSaveInstanceState(Bundle)`**.
```java
private static final String ESTADO_GESTOR_CONTACTOS = "ESTADO_GESTOR_CONTACTOS";

@Override
protected void onSaveInstanceState(Bundle outState) {
    super.onSaveInstanceState(outState);
    outState.putSerializable(ESTADO_GESTOR_CONTACTOS, gestorContactos);
}
```
No `onCreate()` verifica-se se há estado guardado:
```java
if (savedInstanceState == null) {
    this.gestorContactos = new GestorContactos();
    this.gestorContactos.adicionarDadosIniciais();
} else {
    this.gestorContactos = (GestorContactos)
            savedInstanceState.getSerializable(ESTADO_GESTOR_CONTACTOS);
}
listViewContactos = findViewById(R.id.listViewContactos);
atualizarContactos();
listViewContactos.setOnItemClickListener((parent, view, position, id) -> {
    Intent intent = new Intent(getApplicationContext(), DetalhesContactoActivity.class);
    intent.putExtra(GESTOR_CONTACTOS, gestorContactos);          // objeto serializado no intent
    intent.putExtra(DetalhesContacto.POSICAO, position);
    startActivity(intent);
});
```
> Isto **só mantém o estado enquanto a app está ativa**; se o utilizador sair explicitamente e voltar a abrir, perdem-se os dados → é preciso armazenamento persistente.

## 4. Armazenamento interno

Características: **sempre disponível**; ficheiros **privados** (sem acesso do utilizador nem de outras apps); **removido ao desinstalar** a app; **espaço limitado**.

### 4.1 Gravar (`onPause()`)
- `openFileOutput(nome, modo)` → `FileOutputStream`; `new ObjectOutputStream(fos)` → `writeObject(obj)`; `close()` aos streams.
- **Os dados devem ser gravados em `onPause()`.**
```java
@Override
protected void onPause() {
    super.onPause();
    try {
        FileOutputStream fileOutputStream = openFileOutput("contactos.bin", Context.MODE_PRIVATE);
        ObjectOutputStream objectOutputStream = new ObjectOutputStream(fileOutputStream);
        objectOutputStream.writeObject(gestorContactos);
        objectOutputStream.close();
        fileOutputStream.close();
    } catch (IOException e) {
        e.printStackTrace();
        Toast.makeText(this, "Não foi possível escrever contactos no armazenamento interno",
                Toast.LENGTH_SHORT).show();
    }
}
```

### 4.2 Ler (`onCreate()`)
- `openFileInput(nome)` → `FileInputStream`; `new ObjectInputStream(fis)` → `readObject()`; `close()`.
- **Carregar em `onCreate()`:** primeiro ver se há `savedInstanceState`; se não, tentar ler do armazenamento interno.
- Exceções:
  - `FileNotFoundException` — o ficheiro não existe (pode ser a 1.ª execução; **não é necessariamente erro**);
  - `IOException` — problema durante a leitura;
  - `ClassNotFoundException` — ao desserializar.
```java
if (savedInstanceState == null) {
    try {
        FileInputStream fileInputStream = openFileInput("contactos.bin");
        ObjectInputStream objectInputStream = new ObjectInputStream(fileInputStream);
        this.gestorContactos = (GestorContactos) objectInputStream.readObject();
        objectInputStream.close();
        fileInputStream.close();
    } catch (FileNotFoundException e) {
        e.printStackTrace();
        Toast.makeText(this, "Não foi possível ler contactos do armazenamento interno", Toast.LENGTH_SHORT).show();
    } catch (IOException e) {
        e.printStackTrace();
        Toast.makeText(this, "Erro ao ler Contactos do armazenamento interno", Toast.LENGTH_SHORT).show();
    } catch (ClassNotFoundException e) {
        e.printStackTrace();
        Toast.makeText(this, "Erro ao ler Contactos do armazenamento interno", Toast.LENGTH_SHORT).show();
    }
} else {
    this.gestorContactos = (GestorContactos) savedInstanceState.getSerializable(ESTADO_GESTOR_CONTACTOS);
}
if (gestorContactos == null) {
    this.gestorContactos = new GestorContactos();
    // this.gestorContactos.adicionarDadosIniciais();
}
```

## 5. Resumo prático

| Necessidade | Solução vista nas aulas |
|---|---|
| Passar um objeto entre atividades | `Serializable` + `putExtra` / `getSerializableExtra` (ou apenas o **id** + Singleton) |
| Sobreviver à recriação da Activity | `onSaveInstanceState` / `savedInstanceState` |
| Persistir entre execuções (objeto inteiro) | Ficheiro interno com `ObjectOutputStream` (gravar em `onPause`, ler em `onCreate`) |
| Preferências / configuração simples | `SharedPreferences` |
| Dados estruturados e consultáveis | **SQLite** (próximo capítulo) |

## 6. Próximos temas (indicados)
Padrão **Singleton** em Java; **SQLite** (`SQLiteOpenHelper`); a **Room** é mencionada, mas **o enunciado do projeto não a aceita**.

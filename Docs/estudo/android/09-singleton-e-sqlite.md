# Android 09 — Padrão Singleton e Persistência com SQLite

> Fonte: `09_Persistencia_Dados_SQLite.pdf` (Cap. 9). Sem ficha própria no material.
> ⚠ O enunciado exige **SQLite** para a BD local — a biblioteca **Room não é contabilizada**. Este capítulo é o guia do que será avaliado.

---

## 1. Problema que o Singleton resolve

Se os contactos passam a ser **persistentes** e **partilhados por todas as atividades**: *quantas instâncias de `GestorContactos` criamos? onde? quantas queremos?* → só **uma**, com ponto global de acesso.

## 2. Padrão Singleton

- Padrão de desenho que **garante uma única instância** de uma classe e um **ponto global de acesso**; a instância é **partilhada por todos os objetos que lhe acedem**.
- Regras:
  - atributo **estático e privado** do próprio tipo (a instância);
  - **construtor privado** (só chamado dentro da classe);
  - método de acesso **`getInstance()`**: **`static`** (método de classe) e **`synchronized`** (sem acesso simultâneo de 2 threads); só invoca o construtor **se ainda não existir instância**.

```java
public class SingletonContactos {
    private static SingletonContactos INSTANCE = null;
    private LinkedList<Contacto> contactos;

    public static synchronized SingletonContactos getInstance() {
        if (INSTANCE == null) {
            INSTANCE = new SingletonContactos();
        }
        return INSTANCE;
    }

    private SingletonContactos() {                 // construtor privado
        contactos = new LinkedList<>();
        adicionarDadosIniciais();                  // só enquanto os dados não estiverem guardados
    }

    public LinkedList<Contacto> getContactos() { return new LinkedList<>(contactos); }

    // leitura/escrita em ficheiros binários (armazenamento interno)
    public void lerContactos(Context context)    { ... }
    public void gravarContactos(Context context) { ... }

    public void adicionarContacto(Contacto contacto) {
        contacto.setFoto(R.drawable.foto);
        contactos.add(contacto);
    }
}
```

### 2.1 Substituir o `GestorContactos`
1. Substituir **todos** os acessos pelo `SingletonContactos.getInstance()`.
2. Remover o atributo `GestorContactos` de todas as atividades.
3. Remover a classe `GestorContactos`.

```java
// ListaContactosActivity.onCreate
if (savedInstanceState == null) {
    SingletonContactos.getInstance().lerContactos(this);
    this.contactos = SingletonContactos.getInstance().getContactos();
} else {
    this.contactos = (LinkedList<Contacto>) savedInstanceState.getSerializable(ESTADO_CONTACTOS);
}
if (this.contactos == null) {
    this.contactos = SingletonContactos.getInstance().getContactos();
}
```

---

## 3. SQLite no Android

- **SQLite** = BD **relacional, open source**, com comandos SQL; cada app pode criar **várias BDs**, guardadas no sistema (privadas à app).
- Usa-se a API que já vem no SDK:
  - **`SQLiteDatabase`** — métodos de manipulação da BD (executa os comandos SQL);
  - **`SQLiteOpenHelper`** — **cria a BD e gere as versões**.

### 3.1 `SQLiteOpenHelper` — dois métodos obrigatórios
| Método | Quando é chamado / função |
|---|---|
| `onCreate(SQLiteDatabase db)` | Quando a BD é criada **pela primeira vez**: tem as diretrizes de criação (tabelas) e os dados iniciais |
| `onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion)` | Sempre que a BD precisa de ser atualizada (alteração estrutural) para evitar inconsistência entre a BD do dispositivo e a nova |

### 3.2 Classe `ModeloBDHelper`
```java
public class ModeloBDHelper extends SQLiteOpenHelper {

    private static final String DB_NAME = "contactosDB";
    private static final int DB_VERSION = 1;
    private static final String TABLE_NAME = "Contacto";
    public static final String ID_CONTACTO = "id";
    public static final String NOME_CONTACTO = "nome";
    public static final String FOTO_CONTACTO = "foto";

    private final SQLiteDatabase database;

    // recebe só o contexto; o factory fica a null
    public ModeloBDHelper(Context context) {
        super(context, DB_NAME, null, DB_VERSION);
        this.database = this.getWritableDatabase();
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        String createContactoTable = "CREATE TABLE " + TABLE_NAME +
                "( " + ID_CONTACTO + " INTEGER PRIMARY KEY AUTOINCREMENT, " +
                NOME_CONTACTO + " TEXT NOT NULL, " +
                FOTO_CONTACTO + " INTEGER" +
                ");";
        db.execSQL(createContactoTable);
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_NAME);
        this.onCreate(db);
    }
```

### 3.3 CRUD sobre a BD

**Create** — `insert(tabela, nullColumnHack, ContentValues)`; devolve o **id** do registo inserido. 2.º parâmetro: o que fazer se `ContentValues` estiver vazio (se `null`, não insere nenhuma linha).
```java
public void adicionarContacto(Contacto contacto) {
    ContentValues values = new ContentValues();
    values.put(ID_CONTACTO, contacto.getId());
    values.put(NOME_CONTACTO, contacto.getNome());
    values.put(FOTO_CONTACTO, contacto.getFoto());
    this.database.insert(TABLE_NAME, null, values);
}
```
**Read** — todas as consultas devolvem um **`Cursor`**:
```java
public LinkedList<Contacto> getAllContactos() {
    LinkedList<Contacto> contactos = new LinkedList<>();
    Cursor cursor = this.database.rawQuery("SELECT * FROM " + TABLE_NAME, null);
    if (cursor.moveToFirst()) {
        do {
            contactos.add(new Contacto(cursor.getLong(0), cursor.getString(1), cursor.getInt(2)));
        } while (cursor.moveToNext());
        cursor.close();
    }
    return contactos;
}
```
**Update** — devolve o **nº de linhas atualizadas** (sucesso se `> 0`):
```java
public boolean guardarContacto(Contacto contacto) {
    ContentValues values = new ContentValues();
    values.put(ID_CONTACTO, contacto.getId());
    values.put(NOME_CONTACTO, contacto.getNome());
    values.put(FOTO_CONTACTO, contacto.getFoto());
    return this.database.update(TABLE_NAME, values, "id = ?",
            new String[]{"" + contacto.getId()}) > 0;
}
```
**Delete** — apaga se encontrar o id; devolve o nº de registos eliminados:
```java
public void removerContacto(long idContacto) {
    this.database.delete(TABLE_NAME, "id = ?", new String[]{"" + idContacto});
}
```
Outros métodos úteis: **pesquisar por um campo**, **remover todos** (`removerAllContactos`), etc.

> **Boa prática SQL:** usar sempre `?` + array de argumentos (como nos exemplos) em vez de concatenar strings → evita SQL injection.

---

## 4. Onde instanciar a BD e quando gravar

| Pergunta | Resposta do slide |
|---|---|
| Onde criar a instância da BD? | Na classe **Singleton** (ponto de acesso aos dados, disponível a todas as atividades) |
| Quando guardar? | ❌ Em `onPause()` apagando tudo e voltando a inserir (processo "pesado") — ✅ **sempre que se executa uma operação sobre os dados**, cada método do Singleton faz diretamente a sua operação na BD (**opção mais correta**) |
| Como iniciar a BD no Singleton? | Método `iniciarBD(Context)` — não dá para ser no construtor, que não recebe o `Context` |

```java
public class SingletonContactos {
    private static ModeloBDHelper modeloDB = null;

    // inicia a BD passando o contexto, se ainda não tiver sido instanciada
    public static void iniciarBD(Context context) {
        if (modeloDB == null)
            modeloDB = new ModeloBDHelper(context);
    }

    public void lerBD() { this.contactos = modeloDB.getAllContactos(); }

    // (alternativa menos correta) gravar tudo
    public void gravarBD() {
        modeloDB.removerAllContactos();
        for (Contacto contacto : contactos) modeloDB.adicionarContacto(contacto);
    }

    // abordagem correta: cada operação reflete-se logo na BD
    public void adicionarContacto(Contacto contacto) {
        contacto.setFoto(R.drawable.foto);
        contactos.add(contacto);
        modeloDB.adicionarContacto(contacto);          // também na BD
    }

    public void removerContacto(Contacto contacto) {
        if (contactos.contains(contacto)) {
            contactos.remove(contacto);
            modeloDB.removerContacto(contacto.getId()); // também na BD
        }
    }
}
```
Na atividade:
```java
// ListViewContactosActivity.onCreate
SingletonContactos.iniciarBD(this);
if (savedInstanceState == null) {
    SingletonContactos.getInstance().lerBD();
    this.contactos = SingletonContactos.getInstance().getContactos();
} else {
    this.contactos = (LinkedList<Contacto>) savedInstanceState.getSerializable(ESTADO_GESTOR_CONTACTOS);
}
if (this.contactos == null) {
    this.contactos = SingletonContactos.getInstance().getContactos();
}
```

## 5. Desafio do capítulo
Criar uma BD SQLite para gerir os contactos: **ler, gravar, adicionar, remover**.

## 6. Próximo tema (não incluído nos ficheiros da pasta)
O slide anuncia: **API REST e requisitos de acesso com apps Android** — rede, JSON (`org.json`), **Volley**. Esses materiais **não estão** em `Docs/info/android` — ver [`10-api-rest-volley.md`](10-api-rest-volley.md) para o que o enunciado exige e um guia complementar.

# Android 10 — API REST e Volley (guia complementar)

> ⚠ **Este tema NÃO está nos materiais das pastas.** O capítulo 9 termina anunciando "*API REST e requisitos de acesso com apps Android*" (JSON + Volley), mas não há PDF correspondente em `Docs/info/android/`.
> Este documento é **complementar (nosso)**, escrito a partir dos requisitos do enunciado e da documentação pública do Volley/Yii2. **Confirmar com os docentes/slides quando forem publicados.**
> Relevância: **Volley é obrigatório** ("outra biblioteca não é contabilizada") e o acesso à API **não pode bloquear a UI**.

---

## 1. O que o enunciado exige

| Requisito | Implicação |
|---|---|
| Biblioteca **Volley** | Todas as chamadas de rede com Volley |
| **Não bloquear a UI** | Volley já é assíncrono (fila em threads próprias; callbacks na main thread) — **nunca** usar `HttpURLConnection` na main thread |
| **CRUD** em ≥ 4 funcionalidades | `GET` (ler), `POST` (inserir), `PUT/PATCH` (alterar), `DELETE` (remover) |
| ≥ 2 atividades **dinâmicas** | Montar a UI com dados que chegam da API |
| **Master/detail** com serviços da API | `GET /events` → lista; `GET /events/{id}` → detalhe |
| **Menu de configuração** do servidor | URL base em **SharedPreferences** ([`06`](06-menus-sharedpreferences.md)) |
| API **no servidor da UC** | URL configurável (emulador, telemóvel, servidor) |

## 2. Preparação do projeto

`app/build.gradle.kts`:
```kotlin
dependencies {
    implementation("com.android.volley:volley:1.2.1")
}
```
`AndroidManifest.xml`:
```xml
<uses-permission android:name="android.permission.INTERNET" />

<application
    android:usesCleartextTraffic="true"   <!-- só em desenvolvimento (HTTP). Em produção: HTTPS -->
    ... >
```
> **Endereços:** no **emulador**, `localhost` do PC é **`10.0.2.2`**; num telemóvel físico, o IP do PC na rede (`http://192.168.x.x/...`). Por isso o URL é configurável.

## 3. REST em 5 minutos

| Verbo | Operação | Exemplo VolLink |
|---|---|---|
| `GET /events` | listar | feed de eventos |
| `GET /events/{id}` | ler um | detalhe do evento |
| `POST /applications` | criar | candidatar-se a um turno |
| `PUT /profile` | atualizar | editar perfil |
| `DELETE /applications/{id}` | remover | cancelar candidatura |
- Representação **JSON**; **códigos HTTP** significativos (200, 201, 204, 400, 401, 403, 404, 422).
- Autenticação **stateless** por token: `Authorization: Bearer <token>`.

## 4. `RequestQueue` partilhada (Singleton — padrão do capítulo 9)

```java
public class VolleySingleton {
    private static VolleySingleton instance;
    private final RequestQueue requestQueue;

    private VolleySingleton(Context context) {
        // ApplicationContext evita fugas de memória (não guardar a Activity)
        requestQueue = Volley.newRequestQueue(context.getApplicationContext());
    }

    public static synchronized VolleySingleton getInstance(Context context) {
        if (instance == null) instance = new VolleySingleton(context);
        return instance;
    }

    public <T> void add(Request<T> request) { requestQueue.add(request); }
}
```

## 5. Tipos de pedido

### 5.1 `GET` de uma lista JSON (`JsonArrayRequest`)
```java
String url = AppPrefs.getServidor(context) + "/events";
JsonArrayRequest req = new JsonArrayRequest(Request.Method.GET, url, null,
    response -> {
        // response: JSONArray — passar a lista de Evento ao adaptador da RecyclerView
        ArrayList<Evento> eventos = EventoJsonParser.parse(response);
        adaptador.setEventos(eventos);
    },
    error -> mostrarErro(error)) {
        @Override public Map<String, String> getHeaders() {
            Map<String, String> h = new HashMap<>();
            h.put("Authorization", "Bearer " + AppPrefs.getToken(context));
            h.put("Accept", "application/json");
            return h;
        }
    };
VolleySingleton.getInstance(context).add(req);
```
> Se a API devolver um objeto com paginação (`{"items":[...],"_meta":{...}}`), usar `JsonObjectRequest` e ler `response.getJSONArray("items")`.

### 5.2 `POST` com corpo JSON (`JsonObjectRequest`)
```java
JSONObject body = new JSONObject();
try { body.put("shift_id", shiftId); } catch (JSONException e) { /* ignorar */ }

JsonObjectRequest req = new JsonObjectRequest(Request.Method.POST,
    AppPrefs.getServidor(ctx) + "/applications", body,
    response -> Toast.makeText(ctx, "Candidatura submetida", Toast.LENGTH_SHORT).show(),
    error -> mostrarErro(error)) {
        @Override public Map<String, String> getHeaders() { /* Authorization, Content-Type */ ... }
    };
```
### 5.3 `PUT` e `DELETE`
Usar `Request.Method.PUT` / `Request.Method.DELETE` (com `JsonObjectRequest` ou `StringRequest`). Em `DELETE` é comum a API responder **204 sem corpo** → usar `StringRequest` (um `JsonObjectRequest` falha a fazer *parse* de corpo vazio).
### 5.4 Formulário clássico (`StringRequest` + `getParams()`)
```java
StringRequest req = new StringRequest(Request.Method.POST, url,
    response -> { /* resposta em texto */ },
    error -> mostrarErro(error)) {
        @Override protected Map<String, String> getParams() {
            Map<String, String> p = new HashMap<>();
            p.put("email", email);
            p.put("password", password);
            return p;
        }
    };
```
(`getParams()` envia `application/x-www-form-urlencoded`; Yii2 recebe em `$_POST` — para JSON no corpo, configurar o `parsers` do `request`, ver secção 8.)

## 6. Erros e *timeouts*
```java
private void mostrarErro(VolleyError error) {
    if (error instanceof NoConnectionError || error instanceof TimeoutError) {
        // sem rede / servidor inatingível → mensagem clara em PT + eventualmente usar cache SQLite
    } else if (error.networkResponse != null) {
        int code = error.networkResponse.statusCode;
        if (code == 401) { /* limpar token e voltar ao Login */ }
        else if (code == 422) { /* erros de validação no corpo */ }
    }
}
req.setRetryPolicy(new DefaultRetryPolicy(10_000, 1, 1.0f));   // timeout 10 s, 1 retry
```

## 7. Padrão recomendado (MVC + Singleton + SQLite)
```
Activity/Fragment  ──pede──▶  SingletonGestorEventos  ──Volley──▶  API
        ▲                          │   ▲                             │
        └─────callback/listener────┘   └────── guarda cache ◀────────┘
                                   SQLite (ModeloBDHelper)
```
- A UI **não faz Volley diretamente**: chama o Singleton, que devolve os dados por **listener** (`interface EventosListener { void onRefresh(ArrayList<Evento> eventos); }`).
- O Singleton **grava em SQLite** (cache) cada lista recebida e, se falhar a rede, **devolve a cache**.
- `SharedPreferences`: `server_url`, `token`, `user_name` (**nunca a password**).
- Parsing JSON em classes próprias (`EventoJsonParser`) com `org.json` (`JSONObject`/`JSONArray`).

## 8. Lado servidor (Yii2) — esboço (nosso)
O VolLink prevê uma app **`api/`** no Advanced (ver [`11`](11-aplicacao-ao-vollink.md)).
```php
// api/config/main.php (excerto)
'components' => [
    'request' => [
        'parsers' => ['application/json' => 'yii\web\JsonParser'],
        'enableCsrfValidation' => false,            // API stateless
    ],
    'response' => ['format' => yii\web\Response::FORMAT_JSON],
    'user' => ['identityClass' => 'common\models\User', 'enableSession' => false],
    'urlManager' => [
        'enablePrettyUrl' => true, 'showScriptName' => false,
        'rules' => [
            ['class' => 'yii\rest\UrlRule', 'controller' => ['event', 'application', 'notification']],
        ],
    ],
],
```
```php
// api/controllers/EventController.php
class EventController extends \yii\rest\ActiveController {
    public $modelClass = 'common\models\Event';

    public function behaviors() {
        $b = parent::behaviors();
        $b['authenticator'] = ['class' => \yii\filters\auth\HttpBearerAuth::class];   // Authorization: Bearer
        return $b;
    }
}
```
`findIdentityByAccessToken($token)` em `User` devolve o utilizador cujo `auth_key` = token (como no desenho do VolLink). Ver `IdentityInterface` em [`../plataformas/05-autenticacao-acf-rbac.md`](../plataformas/05-autenticacao-acf-rbac.md).

## 9. Extras da **adenda** (só se entregarem em exame normal)
Requisitos adicionais: **upload de imagem (galeria/câmara)** e **PDF por email**.

- **Escolher imagem/foto:** `ActivityResultContracts.GetContent()` ("image/*") e `ActivityResultContracts.TakePicture()` (com URI de `FileProvider`).
- **Upload:** o Volley **não traz `multipart`** de série → criar uma classe que **estende `Request<NetworkResponse>`** (`VolleyMultipartRequest`) a montar o corpo `multipart/form-data`; **continua a ser Volley**. Alternativa: enviar a imagem em **Base64** num campo JSON (mais simples, mais pesado).
- **PDF com dados dinâmicos:** `android.graphics.pdf.PdfDocument` (ou outra lib de PDF) → gravar em `getExternalFilesDir()`.
- **Enviar por email:** `Intent.ACTION_SEND` com `EXTRA_STREAM` (URI via `FileProvider`), `EXTRA_EMAIL`, `EXTRA_SUBJECT`, tipo `application/pdf`, e `resolveActivity` ([`03`](03-activities-intents.md)).

# Android 11 — Aplicação ao VolLink (plano de implementação da app)

> Cruza o **enunciado do AMSI** e a matéria (caps. 1–9) com o desenho já existente em `Docs/src/funcionalidades-android.md`, `requisitos.md` e `arquitetura.md`.
> Estado: o módulo `api/` e a app Android **ainda não existem** no repositório.

---

## 1. Âmbito
App **só para voluntários** (role `voluntario`, permissão `accessMobile`). Não cria eventos, não gere turnos, não decide candidaturas, sem back-office. **Android 8+ (API 26+)** (RNF-12). *As fichas usam **API 27 (Android 8.1)** como mínimo, que é compatível.*

## 2. Mapa **requisito do enunciado → como o cumprimos**

| # | Requisito AMSI | Cumprimento no VolLink | Matéria |
|---|---|---|---|
| 1 | 6–10 atividades/fragments; **alguns com Fragments** | 9 ecrãs (abaixo); **navegação por `DrawerLayout` + fragmentos** (Feed, Minhas candidaturas, Notificações, Perfil, Configurações) e activities para Login/Registo/Detalhe | [`03`](03-activities-intents.md), [`04`](04-fragmentos-navigationview.md) |
| 2 | ≥ 4 funcionalidades com CRUD no webservice | Candidaturas (C/R/D), Perfil (R/U), Notificações (R/U), Eventos (R) | [`10`](10-api-rest-volley.md) |
| 3 | As 4 operações CRUD em alguns dados | **Candidaturas:** `POST`/`GET`/(`PUT` estado)/`DELETE`; **Perfil:** `GET`/`PUT` — garantir **C, R, U e D** completos sobre pelo menos um recurso (ver §4) | [`10`](10-api-rest-volley.md) |
| 4 | ≥ 2 atividades com input | Login, Registo, Candidatura, Perfil, Configurações | [`02`](02-interfaces-layouts.md) |
| 5 | ≥ 2 atividades dinâmicas | Feed, Detalhe, Minhas candidaturas, Notificações (RecyclerView + dados da API) | [`05`](05-mvc-listas-recyclerview.md) |
| 6 | **Volley** | Toda a rede via `VolleySingleton` | [`10`](10-api-rest-volley.md) |
| 7 | UI não bloqueada | Volley assíncrono + *loading/erro*; nada de rede na main thread | [`10`](10-api-rest-volley.md) |
| 8 | **Master/detail** | Feed → Detalhe do evento (`GET /events` → `GET /events/{id}`) | [`05`](05-mvc-listas-recyclerview.md) §2.4 |
| 9 | SharedPreferences + **SQLite** (**sem Room**) | Prefs: `server_url`, `token`, `user_name`; SQLite: cache de eventos e candidaturas | [`06`](06-menus-sharedpreferences.md), [`09`](09-singleton-e-sqlite.md) |
| 10 | MVC, planeamento, comentários | Packages `modelo`, `adaptadores`, `utils`; Singleton de dados; planeamento por membro (Etapa 1) | [`05`](05-mvc-listas-recyclerview.md), [`09`](09-singleton-e-sqlite.md) |
| 11 | **Menu de configuração** do servidor | Ecrã *Configurações* (`server_url`) | [`06`](06-menus-sharedpreferences.md) |
| 12 | API no servidor da UC | URL configurável | [`10`](10-api-rest-volley.md) |
| — | **Compila sem erros** (senão 0) | Testar `Build → Clean/Rebuild` antes de entregar; ver checklist §7 | [`00`](00-enunciado-e-avaliacao.md) |
| — | GitHub com docentes | Adicionar os 4 docentes ao repositório | [`00`](00-enunciado-e-avaliacao.md) |

## 3. Ecrãs

| # | Ecrã | Componente sugerido | API |
|---|---|---|---|
| 1 | Login | `LoginActivity` (launcher) | `POST /api/auth/login` |
| 2 | Registo | `RegistoActivity` | `POST /api/auth/register` |
| 3 | Feed de eventos | `ListaEventosFragment` (`RecyclerView` + `SearchView`/filtros) | `GET /api/events` |
| 4 | Detalhe do evento | `DetalhesEventoActivity` (turnos, vagas, organizador) | `GET /api/events/{id}` |
| 5 | Candidatura a um turno | Diálogo/atividade `CandidaturaActivity` (turno + confirmar) | `POST /api/applications` |
| 6 | Minhas candidaturas | `MinhasCandidaturasFragment` (+ cancelar com `AlertDialog`) | `GET /api/applications`, `DELETE /api/applications/{id}` |
| 7 | Perfil | `PerfilFragment` (nome, competências, histórico e horas) | `GET/PUT /api/profile` |
| 8 | Notificações | `NotificacoesFragment` | `GET /api/notifications`, `PUT /api/notifications/{id}` |
| 9 | Configurações | `ConfiguracoesActivity` | — (SharedPreferences) |

**Estrutura de navegação (padrão da Ficha 4):** `LoginActivity` → `MenuMainActivity` (`DrawerLayout` + `NavigationView` + `FrameLayout fragmentContainer`) → fragmentos 3, 6, 7, 8 + atividades 4, 5, 9.

## 4. Garantir o CRUD "completo" (requisito 3)
O enunciado exige que **sobre alguns dos dados** existam as **4 operações**. No desenho atual:
- **Candidatura:** Create (`POST`), Read (`GET`), **Update** → *(falta no desenho: ex. mudar de turno com `PUT /applications/{id}`, RF-AM-07 "escolher turno")*, Delete (`DELETE`).
- **Perfil:** Read + Update (sem C/D).
- **Notificações:** Read + Update (lida).
👉 **Ação:** incluir o **Update da candidatura** (alterar de turno antes do início) para a candidatura ter C/R/U/D completos; ou documentar claramente que o recurso "Candidatura" cumpre as quatro operações.

## 5. Arquitetura (MVC + Singleton + SQLite + Volley)

```
com.vollink.app
├── modelo/          Evento, Turno, Candidatura, Notificacao, Utilizador
│   ├── SingletonGestorEventos     (lista em memória, lê/grava SQLite, chama Volley)
│   ├── SingletonGestorCandidaturas
│   └── ModeloBDHelper             (SQLiteOpenHelper: tabelas de cache)
├── adaptadores/     ListaEventosAdaptador, ListaCandidaturasAdaptador (RecyclerView.Adapter)
├── listeners/       EventosListener, CandidaturasListener … (callbacks Volley → UI)
├── utils/           AppPrefs (SharedPreferences), VolleySingleton, JsonParser…
├── vistas (Activities/Fragments)  LoginActivity, MenuMainActivity, ListaEventosFragment, …
```
Regras:
- A UI **nunca** faz Volley; pede ao Singleton, que responde por **listener**.
- Cada resposta da API **atualiza a cache SQLite**; sem rede → mostra a cache.
- Tabelas SQLite (cache): `evento(id, titulo, categoria, local, inicio, fim, …)`, `candidatura(id, evento_id, turno_id, estado, …)`; operações **uma a uma** (insert/update/delete), **não** "apagar tudo e reinserir" ([`09`](09-singleton-e-sqlite.md) §4).
- **Autenticação:** `Authorization: Bearer <token>`; em `401` → limpar token (`SharedPreferences`) e voltar ao `LoginActivity`.
- **Nunca guardar a password** em SharedPreferences ([`06`](06-menus-sharedpreferences.md)).

## 6. Plano por etapas (alinhado com o enunciado e as fichas)

| Etapa | Entrega | Base na matéria |
|---|---|---|
| Etapa 1 (prazo 07-11-2026; apresentações 09–13-11-2026 — datas já passadas) | Proposta, mockups, requisitos, planeamento por elemento | [`00`](00-enunciado-e-avaliacao.md) |
| E2.1 | Projeto + `LoginActivity` (validação email/password mínima 4) + `MenuMainActivity` com drawer e fragmentos "vazios" | Fichas 3 e 4 |
| E2.2 | `modelo` + Singleton com dados **de exemplo**; `ListaEventosFragment` com `RecyclerView`; `DetalhesEventoActivity` | Ficha 5 |
| E2.3 | Menu de opções (pesquisa), FAB, `AlertDialog` para cancelar, `ConfiguracoesActivity` + `SharedPreferences` | Ficha 6, cap. 6–7 |
| E2.4 | **Volley** + API real (login/token, feed, detalhe) | [`10`](10-api-rest-volley.md) |
| E2.5 | Candidaturas (POST/GET/PUT/DELETE), perfil, notificações | [`10`](10-api-rest-volley.md) |
| E2.6 | **SQLite** como cache; temas/estilos; polimento; relatório | cap. 7–9 |
| Final | Compilar limpo, relatório PDF, GitHub, testes manuais | [`00`](00-enunciado-e-avaliacao.md) |

## 7. Checklist final de entrega (16-01-2027, 23h59)
- [ ] `Build → Rebuild Project` **sem erros** (senão **0 valores**).
- [ ] ≥ 6 e ≤ ~10 atividades/fragments; **fragments** usados.
- [ ] ≥ 4 funcionalidades com CRUD via **Volley**; **C, R, U, D** cobertos em pelo menos um recurso.
- [ ] ≥ 2 atividades com input; ≥ 2 dinâmicas; **master/detail** funcional.
- [ ] **SharedPreferences** (server_url, token) e **SQLite** (cache); **sem Room**.
- [ ] **Menu de configuração** do servidor a funcionar (testar mudando de URL).
- [ ] Rede nunca na main thread; mensagens de erro claras em PT (RNF-13).
- [ ] **Relatório PDF** com: introdução (contexto, requisitos finais, motivação); desenvolvimento (metodologia, divisão de tarefas, tecnologias, problemas e soluções, justificações, funcionalidades não implementadas); conclusões.
- [ ] **Repositório GitHub** com os docentes adicionados + link do servidor da API.
- [ ] (Só exame normal) upload de imagem + PDF por email; ZIP `AMSI202627_ADENDA_<NR>.ZIP` ≤ 100 MB.
- [ ] Preparar o **Teste Prático Individual** (22-01-2027, 50 min): treinar de memória **Fragment + RecyclerView + Intent + SharedPreferences + SQLite CRUD + Volley GET/POST**.

## 8. Dicas para o **TPI** (50 min a implementar funcionalidades)
1. Ter **snippets** mentais de: `Intent` + `putExtra`; `RecyclerView` + Adapter + ViewHolder + listener; `FragmentManager.replace`; `SharedPreferences.edit().putString().apply()`; `SQLiteOpenHelper` (`onCreate`/`onUpgrade`) + `insert/update/delete/rawQuery`; `JsonObjectRequest`/`JsonArrayRequest` + `getHeaders()`.
2. Lembrar os erros típicos: esquecer `INTERNET` no manifest; `usesCleartextTraffic` em HTTP; não registar a Activity no manifest; sem `LayoutManager` no `RecyclerView` (lista não aparece); usar `==` para comparar Strings; `setTheme` depois de `setContentView`.
3. Compilar **cedo e muitas vezes**; submeter no Moodle **dentro do prazo** (obrigatório).

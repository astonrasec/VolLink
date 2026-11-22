# Documentação de estudo — PLSI (Yii2) e AMSI (Android) aplicada ao VolLink

Compilação **detalhada** de todo o material das pastas `Docs/info/plataformas/` e `Docs/info/android/` (aulas, fichas práticas, enunciados, critérios, escalonamentos), reescrita em Markdown para **estudarmos e implementarmos** o projeto **VolLink**.

> **Gerado em 22-11-2026** a partir dos PDFs/HTML das pastas `Docs/info/plataformas/` e `Docs/info/android/` (entretanto **removidas do repositório** — esta pasta passou a ser a única fonte). Os PDFs foram lidos na íntegra (texto); diagramas/figuras de alguns slides não têm texto — quando relevante foi consultada a imagem (esquema ER da Ficha 4/6 e esquema `user`+RBAC da Aula 05).

## Como ler

1. **Primeiro** o enunciado e a avaliação de cada UC (`00-…`): o que conta para a nota e as regras eliminatórias.
2. **Depois** a matéria por ordem (`01`, `02`, …), fazendo as fichas.
3. **Por fim** o documento "Aplicação ao VolLink" (checklists e arquitetura alvo).

## 🧭 Plataformas de Sistemas de Informação (Yii2) — `plataformas/`

| Doc | Conteúdo | Fonte |
|---|---|---|
| [00](plataformas/00-enunciado-e-avaliacao.md) | UC, **enunciado** (Fase 1/2, testes, entrega), **critérios (20/45/15/10/10 %)**, nota final, calendário | Enunciado, apresentação da UC, escalonamentos |
| [01](plataformas/01-introducao-yii2.md) | PHP puro vs framework, MVC/ORM, o que é o Yii2, ciclo do pedido, Composer, **Ficha 1** | Aula 01, Ficha 1 |
| [02](plataformas/02-core-componentes-routing.md) | `Yii::$app`, routing/`UrlManager`, `Url::to`, `action*`, Request/Response, Session/Cookies, DB, ActiveRecord vs Eloquent, **Ficha 2** | Aula 02, Ficha 2 |
| [03](plataformas/03-migrations-activeform-gii.md) | **Migrations**, `ActiveForm`, **Gii** (6 geradores, CRUD, diff), **Ficha 3** | Aula 03, Ficha 3 |
| [04](plataformas/04-basic-advanced-master-detail.md) | Basic vs **Advanced**, **Master-Detail**, lazy/eager, **Ficha 4** (blog) | Aula 04, Ficha 4 |
| [05](plataformas/05-autenticacao-acf-rbac.md) | Autenticação vs autorização, **ACF**, **RBAC** (Php/DbManager, tabelas), `can()`, **Ficha 5** | Aula 05, Ficha 5 |
| [06](plataformas/06-widgets-dataprovider-softdelete.md) | Widgets, **Data Providers**, DetailView/ListView/GridView, **Soft Delete**, **Ficha 6** | Aula 06, Ficha 6 |
| [07](plataformas/07-gridview-avancado-roles-por-user.md) | `SearchModel`, `andFilterWhere`, colunas/filtros/`ActionColumn`, **`getRolesByUser`** | Aulas 07 e 08 |
| [08](plataformas/08-aplicacao-ao-vollink.md) | **Checklist Fase 1/Fase 2**, RBAC do VolLink, boas práticas de segurança, mapa matéria→feature | Docs do projeto + enunciado |
| [09](plataformas/09-testes-codeception.md) | Testes **unitários** e **funcional** com Codeception (⚠ sem material nas pastas) | Complemento nosso |

## 📱 Acesso Móvel a Sistemas de Informação (Android) — `android/`

| Doc | Conteúdo | Fonte |
|---|---|---|
| [00](android/00-enunciado-e-avaliacao.md) | UC, **avaliação**, **enunciado** (12 requisitos), Etapa 1/2, **adenda** do exame normal, datas | Apresentação, Enunciados, Critérios Etapa 1 |
| [01](android/01-instalacao-e-introducao.md) | Instalação Android Studio, evolução/arquitetura/versões do Android, **Ficha 1**, sintaxe Java | Ficha 0, Cap. 1, Ficha 1, slides Java |
| [02](android/02-interfaces-layouts.md) | Estrutura do projeto, View/ViewGroup, `R`/**ViewBinding**, layouts, **Fichas 2, 2.1, 2.2** | Cap. 2 + fichas |
| [03](android/03-activities-intents.md) | Activities, ciclo de vida, **Intents** explícitos/implícitos, extras, resultado, **Ficha 3 (Books)**, Singleton | Cap. 3, Ficha 3, imagens |
| [04](android/04-fragmentos-navigationview.md) | **Fragments**, comunicação, **DialogFragment**, **NavigationView/Toolbar**, **Ficha 4** | Cap. 4, Ficha 4 |
| [05](android/05-mvc-listas-recyclerview.md) | **MVC**, ListView/GridView/Adapters, **RecyclerView**, master/detail, **Ficha 5** | Cap. 5, Ficha 5 |
| [06](android/06-menus-sharedpreferences.md) | Menus (opções/contexto/popup), Up, **SharedPreferences**, FAB/SearchView/AlertDialog, **Ficha 6** | Cap. 6, Ficha 6 |
| [07](android/07-estilos-temas.md) | Estilos, **temas**, tema em runtime, **FloatingActionButton** | Cap. 7 |
| [08](android/08-persistencia-armazenamento-interno.md) | Opções de armazenamento, **Serializable**, `onSaveInstanceState`, ficheiros internos | Cap. 8 |
| [09](android/09-singleton-e-sqlite.md) | **Singleton**, **SQLite** (`SQLiteOpenHelper`, CRUD, `Cursor`) | Cap. 9 |
| [10](android/10-api-rest-volley.md) | **REST + Volley** (⚠ sem material nas pastas), lado servidor Yii2, extras da adenda | Complemento nosso |
| [11](android/11-aplicacao-ao-vollink.md) | Requisito→cumprimento, **ecrãs**, arquitetura da app, plano, checklist, dicas para o TPI | Docs do projeto + enunciado |

## ⚠ O que **não** estava nos materiais (e como foi tratado)

| Lacuna | Tratamento |
|---|---|
| **Volley / API REST / JSON** (anunciado como próximo tema no fim do cap. 9; **sem PDF**) | Guia complementar nosso em [`android/10`](android/10-api-rest-volley.md), assinalado como tal |
| **Codeception / testes** (sem aula nas pastas; só enunciado e programa) | Guia complementar em [`plataformas/09`](plataformas/09-testes-codeception.md) |
| Fichas 1, 2 e 3 de Plataformas têm **lacunas para preencher** | Respostas **nossas** (marcadas "nossa") |
| Diagramas do ciclo de vida da Activity/Fragment (imagens) | Texto dos slides + tabela de callbacks (complemento assinalado) |
| Datas da Fase 2 e dos exames de PLSI; data da PE2 de AMSI (campos "???") | Registadas como **por confirmar** |
| `index.html` do Moodle (anúncios, inscrições, separadores) | **Só "casca"**, sem conteúdo útil |
| Listas de notas e escalonamentos com **nomes de outros estudantes** | **Não reproduzidas** (dados pessoais); só regras e datas |

## ✍️ Convenções nestes documentos
- Código dos slides **corrigido** quando tinha erros de digitação (`beginTrasaction`, aspas tipográficas, `Class` vs `class`…) — onde isso aconteceu há uma nota.
- **"(nossa/nosso)"** = não vem dos slides; é resposta/esboço/complemento escrito por nós.
- ⚠ = armadilha, requisito eliminatório ou lacuna.
- Todos os documentos têm ligações cruzadas (matéria ↔ projeto).

## 🔑 Resumo do que decide a nota

**PLSI:** Nota = (0,2·PO1 + 0,8·PO2) × TP. Projeto: Front 20 · **Back 45** · **Auth/RBAC 15** · Testes 10 · Qualidade 10. Fase 1 = RBAC + registo + **CRUD de utilizadores com Soft Delete** + templates. Fase 2 = tudo + testes (3 unit + 1 funcional). Entrega: zip + SQL + README com credenciais.

**AMSI:** Nota = 15 % PE1 + 15 % PE2 + 20 % P1 + 50 % P2 × %TPI (mínimo 9,5 em P1 e P2). Projeto: ≥ 6 activities/fragments, **CRUD ×4**, **Volley**, **SharedPreferences + SQLite (sem Room)**, **master/detail**, **menu de config do servidor**, UI não bloqueada, **compila ou 0**. Entrega 16-01-2027; TPI 22-01-2027. **IA proibida nos elementos de avaliação.**

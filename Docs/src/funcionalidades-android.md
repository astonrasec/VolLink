# Funcionalidades Android

A aplicação Android do VolLink é **exclusivamente para voluntários**. Consome a API REST do projeto e cobre o percurso do voluntário: autenticação, consulta de eventos, candidatura, cancelamento, histórico/horas, notificações e perfil.

!!! note "Duas unidades curriculares"
    O **website** e o **back-office** são âmbito de PSI. A **app Android** e o **web service REST** são âmbito de AMSI. A app não cria eventos, não gere turnos, não aprova candidaturas e não tem back-office.

## Âmbito

| Funcionalidade | Android |
|---|:---:|
| Login e registo de voluntário | ✅ |
| Verificação de email | ✅ |
| Recuperar palavra-passe | ✅ |
| Feed de eventos publicados | ✅ |
| Filtrar por categoria e localização | ✅ |
| Detalhe do evento (turnos, vagas, organizador) | ✅ |
| Candidatar-se a um turno | ✅ |
| Cancelar candidatura | ✅ |
| Minhas candidaturas | ✅ |
| Perfil (nome, competências) | ✅ |
| Histórico e total de horas | ✅ |
| Notificações in-app | ✅ |
| Menu de configuração do servidor | ✅ |
| Criar/editar eventos | ❌ |
| Gerir turnos | ❌ |
| Aprovar/rejeitar candidaturas | ❌ |
| Confirmar presenças/horas | ❌ |
| Back-office | ❌ |
| Empresa/coordenador | ❌ |
| Avaliações, denúncias, eventos guardados | ❌ |
| Upload de fotografia | ❌ |
| Notificações push | ❌ (fase 2) |
| iOS | ❌ |

## Requisitos técnicos do AMSI

O enunciado do AMSI exige os pontos abaixo. A app foi desenhada para os cumprir com 9 ecrãs.

| Requisito AMSI | Como é cumprido |
|---|---|
| 6 a 10 atividades (Activity/Fragment), mínimo 6 | 9 ecrãs: Login, Registo, Feed, Detalhe, Candidatura, Minhas candidaturas, Perfil, Notificações, Configurações |
| 4 funcionalidades com CRUD sobre o web service | Candidaturas (criar/ler/cancelar), Perfil (ler/atualizar), Notificações (ler/marcar como lida), Eventos (ler) |
| 2 atividades com input do utilizador | Login/Registo, Candidatura, Perfil |
| 2 atividades dinâmicas com dados da API | Feed, Detalhe, Minhas candidaturas, Notificações |
| 1 relacionamento master/detail | Feed de eventos → Detalhe do evento |
| Volley para acesso à API | Todas as chamadas de rede usam Volley; outras libraries não contam |
| SharedPreferences | Endereço do servidor, token de autenticação e nome do utilizador |
| SQLite | Cache local de eventos e candidaturas para consulta rápida/offline |
| Menu de configuração | Ecrã **Configurações** com o endereço do servidor |
| UI não bloqueante | Volley assíncrono + estados de loading/erro; sem chamadas na main thread |
| API alojada no servidor da UC | Endpoint configurável no ecrã de Configurações |
| Projeto compila sem erros | Requisito eliminatório do AMSI |
| GitHub com docentes | Repositório do projeto com os docentes como membros |

!!! warning "Room não conta"
    O AMSI aceita apenas **SQLite**; a biblioteca **Room** não é contabilizada. O mesmo se aplica a bibliotecas de rede que não sejam **Volley**.

## Ecrãs

| # | Ecrã | Tipo | Dados | API |
|---|---|---|---|---|
| 1 | Login | Activity + input | Email, palavra-passe | `POST /api/auth/login` |
| 2 | Registo | Activity + input | Nome, email, palavra-passe, data de nascimento | `POST /api/auth/register` |
| 3 | Feed de eventos | Activity/Fragment + dinâmico | Lista paginada, filtros | `GET /api/events` |
| 4 | Detalhe do evento | Activity + dinâmico + master/detail | Descrição, turnos, vagas, organizador | `GET /api/events/{id}` |
| 5 | Candidatura a um turno | Activity + input | Turno selecionado + evento | `POST /api/applications` (`shift_id`) |
| 6 | Minhas candidaturas | Activity + dinâmico | Estado, evento, turno | `GET /api/applications`, `DELETE /api/applications/{id}` |
| 7 | Perfil | Activity + input | Nome, competências, histórico, horas | `GET /api/profile`, `PUT /api/profile` |
| 8 | Notificações | Activity + dinâmico | Lista, lida/não lida | `GET /api/notifications`, `PUT /api/notifications/{id}` |
| 9 | Configurações | Activity + input | Endereço do servidor | — (SharedPreferences) |

## CRUD e fluxos

| Operação | Onde | Endpoint |
|---|---|---|
| **Create** | Candidatura, registo | `POST /api/applications`, `POST /api/auth/register` |
| **Read** | Feed, detalhe, candidaturas, perfil, notificações | `GET /api/events`, `GET /api/applications`, `GET /api/profile`, `GET /api/notifications` |
| **Update** | Perfil, notificação lida | `PUT /api/profile`, `PUT /api/notifications/{id}` |
| **Delete** | Cancelar candidatura | `DELETE /api/applications/{id}` |

- **Input:** login/registo, candidatura, perfil, configurações.
- **Dinâmico:** feed, detalhe, minhas candidaturas, notificações.
- **Master/detail:** o item do feed abre o detalhe do evento.
- **Cache local:** o feed e as candidaturas são guardados em SQLite; o token e o servidor em SharedPreferences.

## Autenticação

1. O utilizador faz login em `POST /api/auth/login`.
2. A API devolve um **token** (o `auth_key` do utilizador) e o perfil.
3. A app guarda o token em **SharedPreferences**.
4. Todos os pedidos autenticados enviam `Authorization: Bearer <token>`.
5. Se a API devolver `401`, a app limpa o token e volta ao login.

## Configuração do servidor

O ecrã **Configurações** guarda em SharedPreferences:

- `server_url` — endereço base da API (ex.: `http://192.168.1.10/vollink-api`);
- `token` — token de autenticação;
- `user_name` — nome apresentado no perfil.

O endereço é configurável para permitir testar em emulador, dispositivo físico ou servidor da UC sem recompilar a app.

## Estado

| Item | Estado |
|---|---|
| API REST (`api/`) | ⏳ planeada |
| App Android | ⏳ planeada |
| Ecrãs definidos | ✅ 9 ecrãs |
| Endpoints definidos | ✅ 14 endpoints |
| Modelo de dados local | ✅ SQLite (cache) + SharedPreferences |

Ver [Requisitos](requisitos.md) e [User Stories](user-stories.md).

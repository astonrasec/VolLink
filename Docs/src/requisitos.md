# Requisitos

Esta página é a **fonte de verdade** dos requisitos do MVP. O Word e o site usam os mesmos IDs.

**Legenda:** `Alta` = necessário para o MVP; `Média` = importante, mas pode entrar depois do núcleo; `Baixa` = melhoria.

!!! note "Âmbito"
    O MVP cobre autenticação, perfil, eventos/turnos, candidaturas, presenças/horas, notificações, relatório simples, back-office e app Android de voluntário.

## Requisitos funcionais — Web Front-Office


| # | Requisito | Prioridade |
|---|---|---|
| RF-FO-01 | O sistema deve permitir a autenticação por email e palavra-passe, armazenando a palavra-passe com hash seguro. | Alta |
| RF-FO-02 | A empresa deve poder criar, editar, publicar, cancelar, concluir e arquivar os seus próprios eventos de voluntariado. | Alta |
| RF-FO-03 | O sistema deve considerar o criador do evento como coordenador desse evento, aplicando a regra eventOwner. | Média |
| RF-FO-04 | O sistema deve apresentar um feed de eventos publicados, ordenado por data de início, com paginação. | Alta |
| RF-FO-06 | O utilizador deve poder consultar e editar o seu perfil (nome, competências e data de nascimento). | Alta |
| RF-FO-08 | O sistema deve permitir pesquisar e filtrar eventos por texto, categoria, localização e intervalo de datas. | Alta |
| RF-FO-09 | O coordenador deve poder criar, editar e remover turnos de um evento, definindo data/hora de início e fim e o número de vagas, desde que o turno ainda não tenha presenças registadas. | Média |
| RF-FO-10 | O sistema deve verificar a disponibilidade de vagas no turno e no evento, impedindo candidaturas quando a capacidade do turno ou do evento estiver esgotada ou o prazo tiver passado. | Alta |
| RF-FO-11 | O coordenador deve poder publicar, cancelar, concluir ou arquivar o seu evento, alterando o estado conforme as transições permitidas. | Média |
| RF-FO-12 | O coordenador deve poder registar ou corrigir as horas realizadas e confirmar a presença de um voluntário num turno. | Média |
| RF-FO-15 | O sistema deve permitir recuperar a palavra-passe por email e confirmar o email da conta. | Média |
| RF-FO-16 | O utilizador deve poder alterar a palavra-passe, confirmando a palavra-passe atual. | Alta |
| RF-FO-17 | O coordenador deve poder consultar a lista de candidatos por turno e o estado de cada candidatura. | Alta |
| RF-FO-18 | O voluntário deve poder consultar o histórico das suas participações e o total de horas confirmadas. | Alta |
| RF-FO-19 | O voluntário deve poder cancelar a sua candidatura a um turno até ao início desse turno. | Média |
| RF-FO-20 | O sistema deve permitir o registo de novos utilizadores, com escolha do tipo de conta (voluntário ou empresa); as contas de empresa ficam pendentes até validação pelo SysAdmin. | Alta |
| RF-FO-21 | O coordenador deve poder consultar um resumo do evento com o número de candidaturas, aprovações, presenças e horas confirmadas. | Média |
| RF-FO-22 | A empresa deve poder consultar um dashboard com os seus eventos, candidaturas pendentes, voluntários recrutados e horas geradas. | Alta |
| RF-FO-23 | O sistema deve apresentar um perfil público da empresa com nome, descrição, localização, website e selo de verificação. | Média |
| RF-FO-24 | A empresa deve poder editar o perfil público da organização (descrição, website e localização). | Média |


## Requisitos funcionais — Web Back-Office


| # | Requisito | Prioridade |
|---|---|---|
| RF-BO-01 | O SysAdmin deve poder criar, editar e desativar utilizadores. | Alta |
| RF-BO-02 | O SysAdmin deve poder moderar eventos publicados, cancelando ou arquivando os que violem as regras da plataforma. | Média |
| RF-BO-04 | O SysAdmin deve poder suspender um utilizador, indicando o motivo e a duração da suspensão. | Alta |
| RF-BO-05 | O SysAdmin deve poder consultar estatísticas básicas da plataforma (utilizadores, eventos, candidaturas e horas). | Alta |
| RF-BO-10 | O SysAdmin deve poder alterar o estado de um utilizador (ativo, suspenso ou inativo) e consultar a data do último login. | Alta |
| RF-BO-15 | O SysAdmin deve poder validar ou rejeitar contas de empresa, indicando o motivo da rejeição. | Alta |


## Requisitos funcionais — Android


| # | Requisito | Prioridade |
|---|---|---|
| RF-AM-01 | A aplicação Android deve permitir a autenticação por email e palavra-passe. | Alta |
| RF-AM-02 | A aplicação Android deve listar os eventos publicados com paginação. | Alta |
| RF-AM-03 | A aplicação Android deve permitir ao voluntário candidatar-se a um turno. | Alta |
| RF-AM-04 | A aplicação Android deve apresentar os detalhes do evento, incluindo turnos, vagas e organizador. | Alta |
| RF-AM-07 | A aplicação Android deve permitir ao voluntário consultar o seu perfil, histórico de participações e total de horas. | Alta |
| RF-AM-08 | A aplicação Android deve permitir ao voluntário cancelar a sua candidatura a um turno até ao início desse turno. | Média |
| RF-AM-09 | A aplicação Android deve permitir ao voluntário escolher um turno disponível no momento da candidatura. | Média |
| RF-AM-10 | A aplicação Android deve enviar um lembrete antes do início do turno em que o voluntário está inscrito. | Média |
| RF-AM-11 | A aplicação Android deve notificar o voluntário quando a sua candidatura for aprovada ou rejeitada. | Média |
| RF-AM-12 | A aplicação Android deve permitir filtrar o feed por categoria e localização. | Média |


### Requisitos técnicos da app Android (AMSI)


- 6 a 10 atividades (Activity e/ou Fragment), com o mínimo de 6.
- 4 funcionalidades com operações CRUD sobre o web service.
- 2 atividades com introdução de dados pelo utilizador.
- 2 atividades dinâmicas com dados obtidos da API.
- 1 relacionamento master/detail (lista de eventos → detalhe do evento).
- Volley para acesso à API; outras bibliotecas de rede não contam para avaliação.
- SharedPreferences para o endereço do servidor e o token; SQLite para cache local (Room não conta).
- Menu de configuração para o endereço do servidor.
- O acesso à API não pode bloquear a interface gráfica.
- API alojada no servidor da UC; projeto Android Studio compila sem erros; GitHub com os docentes.


## Requisitos não funcionais


| # | Requisito | Tipo | Prioridade |
|---|---|---|---|
| RNF-01 | A interface deve permitir navegação entre páginas principais em máximo 3 cliques. | Usabilidade | Alta |
| RNF-02 | Todos os formulários devem exibir mensagens de erro claras e em português. | Usabilidade | Média |
| RNF-03 | As palavras-passe devem ser guardadas com hash seguro (bcrypt ou Argon2). | Segurança | Alta |
| RNF-04 | O sistema deve prevenir ataques XSS e SQL Injection nas entradas. | Segurança | Alta |
| RNF-06 | O tempo de resposta das páginas não deve exceder 2 segundos. | Eficiência | Média |
| RNF-07 | O sistema deve funcionar nos browsers Chrome, Firefox e Edge na versão atual. | Portabilidade | Alta |
| RNF-08 | A comunicação entre cliente e servidor deve usar HTTPS (TLS 1.2 ou superior) em produção. | Segurança | Alta |
| RNF-09 | A palavra-passe deve ter no mínimo 8 caracteres, com letras e números. | Segurança | Média |
| RNF-10 | A sessão deve expirar após 30 minutos de inatividade. | Segurança | Média |
| RNF-12 | Os dados pessoais devem cumprir o RGPD, permitindo acesso, correção e desativação/anonimização da conta. | Privacidade | Alta |
| RNF-14 | As datas devem ser guardadas em UTC e apresentadas no fuso horário do utilizador. | Consistência | Média |
| RNF-16 | A aplicação Android deve funcionar em Android 8 ou superior. | Portabilidade | Alta |
| RNF-18 | A aplicação Android deve apresentar mensagens de erro claras em português. | Usabilidade | Média |
| RNF-20 | Os logs de erro não devem conter dados pessoais nem palavras-passe. | Privacidade | Média |
| RNF-21 | O sistema deve ser desenvolvido em PHP 8.2+ com Yii2 e MySQL/MariaDB; a app Android deve usar Volley, SharedPreferences e SQLite. | Desenvolvimento | Alta |
| RNF-22 | A API deve ser REST/JSON e usar autenticação por token. | Interoperabilidade | Alta |
| RNF-23 | O projeto Android Studio deve compilar sem erros. | Desenvolvimento | Alta |


## Matriz de cruzamento

`x` = o role pode executar; `-` = não pode. O **Coordenador** é um perfil derivado (quem criou o evento), não uma role RBAC.


| # | Requisito | Sys Admin | Visitante | Voluntário | Coordenador | Empresa |
|---|---|---|---|---|---|---|
| RF-FO-01 | Autenticação por email e palavra-passe | x | - | x | x | x |
| RF-FO-02 | Criar e gerir os próprios eventos | - | - | - | x | x |
| RF-FO-03 | Criador é o coordenador do evento | - | - | - | x | x |
| RF-FO-04 | Feed de eventos ordenado por data | x | x | x | x | x |
| RF-FO-06 | Consultar e editar o perfil | x | - | x | x | x |
| RF-FO-08 | Pesquisar e filtrar eventos | x | x | x | x | x |
| RF-FO-09 | Criar, editar e remover turnos | - | - | - | x | x |
| RF-FO-10 | Verificar vagas no turno e no evento | - | - | x | x | x |
| RF-FO-11 | Publicar, cancelar, concluir ou arquivar evento | - | - | - | x | x |
| RF-FO-12 | Registar horas e confirmar presença | - | - | - | x | x |
| RF-FO-15 | Recuperar palavra-passe e confirmar email | x | x | x | x | x |
| RF-FO-16 | Alterar palavra-passe | x | - | x | x | x |
| RF-FO-17 | Consultar candidatos por turno e estados | - | - | - | x | x |
| RF-FO-18 | Histórico de participações e horas | - | - | x | x | - |
| RF-FO-19 | Cancelar candidatura ao turno | - | - | x | x | - |
| RF-FO-20 | Registo com tipo de conta | - | x | - | - | - |
| RF-FO-21 | Resumo do evento (candidaturas, presenças, horas) | - | - | - | x | x |
| RF-FO-22 | Dashboard da empresa (eventos, candidaturas, voluntários, horas) | - | - | - | x | x |
| RF-FO-23 | Perfil público da empresa com selo de verificação | x | x | x | x | x |
| RF-FO-24 | Editar perfil público da organização | - | - | - | x | x |
| RF-BO-01 | Criar, editar e desativar utilizadores | x | - | - | - | - |
| RF-BO-02 | Moderar eventos publicados | x | - | - | - | - |
| RF-BO-04 | Suspender utilizador com motivo e duração | x | - | - | - | - |
| RF-BO-05 | Estatísticas básicas da plataforma | x | - | - | - | - |
| RF-BO-10 | Alterar estado do utilizador e ver último login | x | - | - | - | - |
| RF-BO-15 | Validar ou rejeitar contas de empresa | x | - | - | - | - |
| RF-AM-01 | Autenticação na app Android | - | - | x | - | - |
| RF-AM-02 | Listar eventos na app Android | - | - | x | - | - |
| RF-AM-03 | Candidatar-se a um turno na app Android | - | - | x | - | - |
| RF-AM-04 | Detalhes do evento na app Android | - | - | x | - | - |
| RF-AM-07 | Perfil, histórico e horas na app Android | - | - | x | - | - |
| RF-AM-08 | Cancelar candidatura ao turno na app Android | - | - | x | - | - |
| RF-AM-09 | Escolher turno na candidatura Android | - | - | x | - | - |
| RF-AM-10 | Lembrete antes do turno | - | - | x | - | - |
| RF-AM-11 | Notificar estado da candidatura | - | - | x | - | - |
| RF-AM-12 | Filtrar feed por categoria e localização | - | - | x | - | - |


## Rastreabilidade

| Requisito | Onde vive |
|---|---|
| RF-FO-01, RF-FO-15, RF-FO-16, RF-FO-20 | `user`, `user_details` |
| RF-FO-02, RF-FO-03, RF-FO-04, RF-FO-08, RF-FO-11 | `event` (+ `EventOwnerRule`) |
| RF-FO-09, RF-FO-10 | `shift` (`capacity`), `event.capacity` |
| RF-FO-12, RF-FO-18, RF-FO-21 | `attendance` (+ `application`) |
| RF-FO-17, RF-FO-19 | `application` |
| RF-FO-06 | `user_details` |
| RF-FO-22 | `event`, `application`, `attendance` (queries) |
| RF-FO-23, RF-FO-24 | `user_details` (`description`, `website`, `location`) |
| RF-BO-01, RF-BO-04, RF-BO-10, RF-BO-15 | `user`, `user_details` |
| RF-BO-02 | `event.status` |
| RF-BO-05 | Queries de contagem |
| RF-AM-01 a RF-AM-12 | API REST + `user`, `event`, `application`, `attendance`, `notification` |

## Endpoints da API

| Verbo | Endpoint | Descrição |
|---|---|---|
| POST | `/api/auth/register` | Registar um voluntário |
| POST | `/api/auth/login` | Autenticar e obter token |
| POST | `/api/auth/verify-email` | Confirmar o email da conta |
| POST | `/api/auth/forgot-password` | Pedir recuperação de palavra-passe |
| GET | `/api/events` | Listar eventos publicados |
| GET | `/api/events/{id}` | Obter detalhe do evento |
| POST | `/api/applications` | Submeter candidatura a um turno |
| GET | `/api/applications` | Listar as candidaturas do voluntário |
| DELETE | `/api/applications/{id}` | Cancelar uma candidatura |
| GET | `/api/profile` | Obter perfil e total de horas |
| PUT | `/api/profile` | Atualizar perfil |
| GET | `/api/notifications` | Listar notificações |
| PUT | `/api/notifications/{id}` | Marcar notificação como lida |
| GET | `/api/info` | Informação pública da API |


Ver também: [Funcionalidades Web](funcionalidades-web.md), [Funcionalidades Android](funcionalidades-android.md), [User Stories](user-stories.md), [Base de dados](base-de-dados.md) e [Permissões](permissoes.md).

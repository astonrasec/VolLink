# User Stories

As user stories estão estimadas em **Story Points** (Fibonacci: 1, 2, 3, 5, 8, 13) e cobrem os requisitos do MVP.

!!! note "Formato"
    `Como <persona>, quero <objetivo> para <benefício>`. Os critérios de aceitação evitam decisões de interface; descrevem comportamento.


## US1 — Consultar eventos de voluntariado

**Story Points:** 3 · **Plataforma:** Web + Android

Como Visitante, quero explorar a lista de eventos publicados para encontrar iniciativas alinhadas com os meus interesses antes de criar conta.

**Critérios de aceitação:**

- Visualizar eventos publicados sem autenticação.
- Filtrar por categoria, localização e intervalo de datas.
- Abrir o detalhe com descrição, turnos, vagas e requisitos.
- O mesmo feed deve estar disponível na app Android após autenticação.

**Requisitos:** `RF-FO-04`, `RF-FO-08`, `RF-AM-02`, `RF-AM-04`, `RF-AM-12`

## US2 — Registar conta de voluntário

**Story Points:** 5 · **Plataforma:** Web + Android

Como Voluntário, quero criar uma conta pessoal para me candidatar a eventos e gerir a minha participação.

**Critérios de aceitação:**

- Selecionar o tipo de conta 'Voluntário'.
- Nome completo, email, palavra-passe e data de nascimento são obrigatórios.
- O email deve ser único.
- O sistema envia um email de confirmação; a conta fica ativa após a confirmação.
- O login por email e palavra-passe funciona no website e na app Android.

**Requisitos:** `RF-FO-01`, `RF-FO-20`, `RF-AM-01`

## US3 — Candidatar-me a um turno

**Story Points:** 5 · **Plataforma:** Web + Android

Como Voluntário, quero candidatar-me a um turno de um evento para garantir a minha participação.

**Critérios de aceitação:**

- Só é possível com conta de voluntário ativa e sessão iniciada.
- O evento tem de estar PUBLISHED e o turno dentro do prazo de candidatura.
- O voluntário escolhe um turno disponível.
- O sistema impede candidaturas quando a capacidade do turno ou do evento está esgotada.
- Não são permitidas candidaturas duplicadas do mesmo voluntário ao mesmo turno.
- O voluntário pode candidatar-se a mais do que um turno do mesmo evento.

**Requisitos:** `RF-FO-10`, `RF-AM-03`, `RF-AM-09`

## US4 — Receber notificações sobre candidaturas

**Story Points:** 3 · **Plataforma:** Web + Android

Como Voluntário, quero receber lembretes e atualizações sobre as minhas candidaturas para não faltar aos compromissos.

**Critérios de aceitação:**

- Notificar quando a candidatura é aprovada, rejeitada ou cancelada.
- Enviar um lembrete 24 horas antes do início do turno.
- As notificações ficam disponíveis na app Android e no website.

**Requisitos:** `RF-AM-10`, `RF-AM-11`

## US5 — Criar e publicar evento

**Story Points:** 8 · **Plataforma:** Web

Como Coordenador (empresa), quero criar e publicar eventos para angariar voluntários para a minha organização.

**Critérios de aceitação:**

- Título, descrição, categoria, localização, data/hora, prazo, vagas e competências são obrigatórios.
- O coordenador pode definir um ou mais turnos, com início, fim e vagas.
- A data do evento tem de ser posterior à data de criação e o prazo anterior ao início.
- O evento fica visível publicamente após a publicação.

**Requisitos:** `RF-FO-02`, `RF-FO-09`, `RF-FO-11`

## US6 — Validar candidaturas de voluntários

**Story Points:** 5 · **Plataforma:** Web

Como Coordenador (empresa), quero analisar e aprovar ou rejeitar candidaturas por turno para selecionar o perfil adequado.

**Critérios de aceitação:**

- O coordenador só pode validar candidaturas dos turnos dos eventos que criou.
- Deve conseguir ver o perfil e as competências do voluntário.
- O sistema impede aprovações acima da capacidade do turno e do evento.
- O voluntário é notificado do resultado.

**Requisitos:** `RF-FO-10`, `RF-FO-17`

## US7 — Registar e validar assiduidade

**Story Points:** 5 · **Plataforma:** Web

Como Coordenador (empresa), quero registar a presença e as horas cumpridas para comprovar a participação.

**Critérios de aceitação:**

- Só é possível registar presenças de candidaturas aprovadas.
- O coordenador regista as horas por turno e pode corrigi-las.
- O total de horas do voluntário é atualizado após a confirmação.
- O coordenador só pode confirmar presenças nos eventos que criou.

**Requisitos:** `RF-FO-12`

## US8 — Validar registo de novas empresas

**Story Points:** 5 · **Plataforma:** Web (back-office)

Como Sys Admin, quero analisar e aprovar pedidos de adesão de empresas para garantir a legitimidade dos eventos.

**Critérios de aceitação:**

- A conta da empresa permanece PENDING até à aprovação.
- O Sys Admin aprova ou rejeita, indicando o motivo da rejeição.
- A empresa recebe uma notificação por email com o resultado.

**Requisitos:** `RF-BO-15`

## US9 — Consultar relatório do evento

**Story Points:** 5 · **Plataforma:** Web

Como Coordenador (empresa), quero consultar um resumo da participação e das horas dos meus eventos para demonstrar o impacto social.

**Critérios de aceitação:**

- O relatório considera apenas os eventos criados pelo coordenador.
- Apresenta o número de candidaturas, aprovações, presenças e horas confirmadas.
- Pode ser filtrado por evento e por período.
- Não inclui exportação CSV no MVP.

**Requisitos:** `RF-FO-21`

## US10 — Registar conta de empresa

**Story Points:** 5 · **Plataforma:** Web

Como Empresa, quero registar a conta da minha organização para criar e gerir eventos.

**Critérios de aceitação:**

- Selecionar o tipo de conta 'Empresa'.
- Nome da empresa, email e palavra-passe são obrigatórios.
- O email deve ser único.
- A conta fica PENDING até validação do Sys Admin.
- Só pode criar e publicar eventos após a validação.
- O sistema envia um email de confirmação do endereço.

**Requisitos:** `RF-FO-20`, `RF-BO-15`

## US11 — Cancelar candidatura a um turno

**Story Points:** 3 · **Plataforma:** Web + Android

Como Voluntário, quero cancelar a minha candidatura a um turno para libertar a vaga quando já não posso participar.

**Critérios de aceitação:**

- O cancelamento é permitido até ao início do turno.
- A vaga fica novamente disponível no turno.
- O voluntário recebe confirmação e o coordenador é notificado.

**Requisitos:** `RF-FO-19`, `RF-AM-08`

## US12 — Gerir perfil e palavra-passe

**Story Points:** 5 · **Plataforma:** Web + Android

Como Utilizador autenticado, quero consultar e editar o meu perfil e alterar a palavra-passe para manter os dados atualizados e a conta segura.

**Critérios de aceitação:**

- Editar nome, competências e data de nascimento.
- Alterar a palavra-passe confirmando a palavra-passe atual.
- Recuperar a palavra-passe por email.
- A nova palavra-passe cumpre os requisitos mínimos.
- O sistema confirma o sucesso da alteração.

**Requisitos:** `RF-FO-06`, `RF-FO-15`, `RF-FO-16`

## US16 — Consultar histórico e horas

**Story Points:** 5 · **Plataforma:** Web + Android

Como Voluntário, quero consultar o histórico das minhas participações e o total de horas confirmadas para acompanhar o meu percurso.

**Critérios de aceitação:**

- O histórico apresenta eventos e turnos em que participei.
- Apresenta o estado de cada participação (confirmada, cancelada ou pendente).
- O total de horas é calculado a partir das presenças confirmadas.
- Disponível no website e na app Android, apenas para o próprio voluntário.

**Requisitos:** `RF-FO-18`, `RF-AM-07`

## US17 — Moderar eventos

**Story Points:** 5 · **Plataforma:** Web (back-office)

Como Sys Admin, quero moderar eventos publicados para garantir o cumprimento das regras da plataforma.

**Critérios de aceitação:**

- Consultar a lista de eventos publicados.
- Cancelar ou arquivar qualquer evento que viole as regras.
- Notificar o criador da decisão.
- A moderação não depende de denúncias (fora do âmbito do MVP).

**Requisitos:** `RF-BO-02`

## US18 — Gerir utilizadores e suspensões

**Story Points:** 8 · **Plataforma:** Web (back-office)

Como Sys Admin, quero gerir utilizadores e aplicar suspensões para manter a plataforma segura.

**Critérios de aceitação:**

- Criar, editar e desativar utilizadores.
- Suspender um utilizador com motivo e duração.
- Alterar o estado (ativo, suspenso ou inativo) e consultar o último login.
- O utilizador suspenso não consegue autenticar-se até ao fim da suspensão.

**Requisitos:** `RF-BO-01`, `RF-BO-04`, `RF-BO-10`

## US19 — Consultar dashboard da empresa

**Story Points:** 5 · **Plataforma:** Web

Como Empresa, quero consultar um dashboard com os meus eventos, candidaturas pendentes, voluntários recrutados e horas geradas para acompanhar o impacto da organização.

**Critérios de aceitação:**

- O dashboard mostra apenas os eventos e candidaturas da empresa.
- Apresenta eventos ativos, candidaturas pendentes, voluntários recrutados e total de horas.
- Apresenta a taxa de aprovação das candidaturas.
- Disponível no website, na área da empresa.

**Requisitos:** `RF-FO-22`

## US20 — Editar perfil público da organização

**Story Points:** 5 · **Plataforma:** Web

Como Empresa, quero editar o perfil público da organização para dar a conhecer a missão e facilitar o contacto com voluntários.

**Critérios de aceitação:**

- Editar nome, descrição, website e localização.
- O perfil é público e mostra o selo de verificação quando a conta está aprovada.
- O perfil lista os eventos publicados da empresa.
- Só a empresa pode editar o seu próprio perfil.

**Requisitos:** `RF-FO-23`, `RF-FO-24`

## Cobertura requisitos → user stories

| Requisito | User stories |
|---|---|
| `RF-FO-01` | US2 |
| `RF-FO-02` | US5 |
| `RF-FO-03` | — |
| `RF-FO-04` | US1 |
| `RF-FO-06` | US12 |
| `RF-FO-08` | US1 |
| `RF-FO-09` | US5 |
| `RF-FO-10` | US3, US6 |
| `RF-FO-11` | US5 |
| `RF-FO-12` | US7 |
| `RF-FO-15` | US12 |
| `RF-FO-16` | US12 |
| `RF-FO-17` | US6 |
| `RF-FO-18` | US16 |
| `RF-FO-19` | US11 |
| `RF-FO-20` | US2, US10 |
| `RF-FO-21` | US9 |
| `RF-FO-22` | US19 |
| `RF-FO-23` | US20 |
| `RF-FO-24` | US20 |
| `RF-BO-01` | US18 |
| `RF-BO-02` | US17 |
| `RF-BO-04` | US18 |
| `RF-BO-05` | — |
| `RF-BO-10` | US18 |
| `RF-BO-15` | US8, US10 |
| `RF-AM-01` | US2 |
| `RF-AM-02` | US1 |
| `RF-AM-03` | US3 |
| `RF-AM-04` | US1 |
| `RF-AM-07` | US16 |
| `RF-AM-08` | US11 |
| `RF-AM-09` | US3 |
| `RF-AM-10` | US4 |
| `RF-AM-11` | US4 |
| `RF-AM-12` | US1 |


!!! note "Requisitos sem US dedicada"
    - `RF-FO-03` é uma regra de sistema (criador = coordenador), coberta pelas US5 e US6.
    - `RF-BO-05` (dashboard) é uma funcionalidade transversal do back-office; não tem US própria.
    - Os requisitos removidos (RF-FO-05, 07, 13, 14; RF-BO-03, 06–09, 11–14; RF-AM-05, 06, 13, 14) estão listados em [Requisitos](requisitos.md#requisitos-removidos-fase-2).

## User stories removidas

| ID | Título | Motivo |
|---|---|---|
| US13 | Avaliar organizador | Sem `event_review`; fase 2 |
| US14 | Denunciar evento ou utilizador | Sem `report`; fase 2 |
| US15 | Guardar eventos de interesse | Sem `saved_event`; fase 2 |

Ver [Requisitos](requisitos.md) e [Funcionalidades Web](funcionalidades-web.md) / [Android](funcionalidades-android.md).

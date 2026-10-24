# VolLink

**Plataforma de Gestão de Voluntariado**

O VolLink liga voluntários, empresas e organizações a eventos de voluntariado. A aplicação permite criar e divulgar eventos, organizar turnos, receber candidaturas, confirmar presenças e contabilizar horas — com regras de acesso distintas para cada perfil.

## Duas interfaces, dois âmbitos

| | [Website](funcionalidades-web.md) | [Android](funcionalidades-android.md) |
|---|---|---|
| **Para quem** | Visitantes, voluntários, empresas, coordenadores e SysAdmin | Voluntários |
| **O que faz** | Front-office completo + back-office | Consumir eventos, candidatar-se, cancelar, ver histórico/horas, notificações e perfil |
| **O que não faz** | — | Criar eventos, gerir turnos, aprovar candidaturas, back-office, empresa |
| **Stack** | PHP 8.2 + Yii2 + MySQL/MariaDB + Bootstrap 5 | Android Studio + Volley + SQLite + SharedPreferences |
| **Consome** | Base de dados via Yii2 | API REST do VolLink |

## Roles

| Role | O que pode fazer |
|---|---|
| **Visitante** | Consultar os eventos publicados, sem conta |
| **Voluntário** | Website + Android: candidatar-se, cancelar, ver histórico/horas e gerir o perfil. **Não cria nem gere eventos.** |
| **Empresa** | Website: criar e gerir eventos, decidir candidaturas, confirmar presenças, editar o perfil público e consultar o dashboard de impacto; **não** se candidata. |
| **Coordenador do evento** | Perfil **derivado** do utilizador da empresa que criou o evento — gere apenas os seus eventos. Não é uma role RBAC. |
| **SysAdmin** | Back-office: gere utilizadores, valida empresas, modera eventos e consulta estatísticas. |

## Módulos

- **Autenticação** — registo, verificação de email, login por email, recuperação e alteração de palavra-passe
- **Perfil** — nome, competências, data de nascimento, histórico e horas
- **Eventos** — criação, edição, publicação, cancelamento, conclusão e arquivo pela **empresa**
- **Turnos** — períodos com horários e vagas próprias
- **Candidaturas** — submissão, cancelamento, aprovação e rejeição
- **Presenças** — registo e confirmação de horas pelo coordenador
- **Relatório do evento** — resumo de candidaturas, aprovações, presenças e horas
- **Perfil público da empresa** — descrição, website, localização e selo de verificação
- **Dashboard da empresa** — eventos, candidaturas, voluntários e horas
- **Notificações** — in-app e email
- **Back-office** — moderação de eventos, gestão de contas e validação de empresas
- **App Android** — percurso do voluntário, com API REST

## Stack

| Camada | Tecnologia |
|---|---|
| Linguagem | PHP 8.2+ |
| Framework | Yii2 Advanced (`~2.0.54`) |
| Base de dados | MySQL / MariaDB — `vollink_db` |
| Interface web | Bootstrap 5 |
| Permissões | RBAC com `DbManager` |
| API | Yii2 REST (`api/`) — planeada |
| Android | Android Studio, Volley, SQLite, SharedPreferences — planeada |
| Documentação | MkDocs + Material |

## Por onde começar

<div class="grid cards" markdown>

- **Instalar o projeto**

    ---

    Requisitos, base de dados, migrações, RBAC e Apache.

    [→ Instalação](instalacao.md)

- **Perceber a arquitetura**

    ---

    Aplicações Yii2, API REST, app Android e fluxo de um pedido.

    [→ Arquitetura](arquitetura.md)

- **Funcionalidades Web**

    ---

    Front-office e back-office, com regras e estados.

    [→ Funcionalidades Web](funcionalidades-web.md)

- **Funcionalidades Android**

    ---

    Ecrãs, CRUD, master/detail e requisitos do AMSI.

    [→ Funcionalidades Android](funcionalidades-android.md)

- **Requisitos**

    ---

    RF web/Android, RNF, matriz de roles e rastreabilidade.

    [→ Requisitos](requisitos.md)

- **User Stories**

    ---

    Histórias com Story Points, critérios e cobertura.

    [→ User Stories](user-stories.md)

- **Mockups**

    ---

    Referência visual do backoffice, frontoffice e Android.

    [→ Mockups](mockups.md)

</div>

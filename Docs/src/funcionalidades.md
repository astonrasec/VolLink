# Funcionalidades

O VolLink tem duas interfaces com âmbitos diferentes:

- **[Website](funcionalidades-web.md)** — front-office (visitante, voluntário, empresa e coordenador) e back-office (`sysAdmin`).
- **[Aplicação Android](funcionalidades-android.md)** — apenas voluntários; consome a API REST.

## Âmbito por interface

| Módulo | Website | Android |
|---|:---:|:---:|
| Autenticação (registo, verificação de email, login, recuperação de password) | ✅ | ✅ |
| Perfil (nome, competências, data de nascimento) | ✅ | ✅ |
| Histórico de participações e horas | ✅ | ✅ |
| Feed de eventos publicados | ✅ | ✅ |
| Pesquisa/filtros por categoria, localização e datas | ✅ | ✅ (categoria e localização) |
| Detalhe do evento (turnos, vagas, organizador) | ✅ | ✅ |
| Criar/editar/publicar/cancelar/concluir/arquivar evento | ✅ (empresa) | ❌ |
| Gerir turnos e vagas | ✅ | ❌ |
| Candidatar-se a um turno | ✅ | ✅ |
| Cancelar candidatura | ✅ | ✅ |
| Aprovar/rejeitar candidaturas | ✅ | ❌ |
| Registar presenças e horas | ✅ | ❌ |
| Consultar presenças/horas | ✅ | ✅ |
| Relatório simples do evento | ✅ | ❌ |
| Perfil público da empresa | ✅ | ❌ |
| Dashboard de impacto da empresa | ✅ | ❌ |
| Notificações in-app | ✅ | ✅ |
| Email transacional | ✅ | — |
| Back-office (utilizadores, empresas, moderação) | ✅ | ❌ |
| Avaliações, denúncias, eventos guardados | ❌ (fase 2) | ❌ (fase 2) |
| Check-in/check-out | ❌ (fase 2) | ❌ (fase 2) |

## Roles

| Role | Interface |
|---|---|
| Visitante | Website (público) |
| Voluntário | Website + Android |
| Empresa | Website |
| Coordenador do evento | Website (perfil derivado, não é role) |
| SysAdmin | Website (back-office) |

## Módulos

### Website

1. **Autenticação e conta** — registo de voluntário/empresa, verificação de email, login por email, recuperação e alteração de palavra-passe.
2. **Perfil** — nome, competências, data de nascimento, histórico e horas.
3. **Eventos e turnos** — feed, pesquisa/filtros, detalhe, criação e ciclo de vida pela empresa, turnos com vagas.
4. **Candidaturas** — submissão, cancelamento, aprovação/rejeição, controlo de capacidade.
5. **Presenças e horas** — registo pelo coordenador, confirmação, total por voluntário.
6. **Relatório do evento** — resumo de candidaturas, aprovações, presenças e horas.
7. **Notificações** — in-app e email.
8. **Perfil público da empresa** — descrição, website, localização e selo de verificação.
9. **Dashboard da empresa** — eventos, candidaturas, voluntários e horas.
10. **Back-office** — dashboard, utilizadores, empresas e moderação de eventos.

### Android

1. **Login/registo** de voluntário.
2. **Feed de eventos** com filtros.
3. **Detalhe do evento** (master/detail).
4. **Candidatura** e **cancelamento**.
5. **Minhas candidaturas**.
6. **Perfil, histórico e horas**.
7. **Notificações in-app**.
8. **Configurações** — endereço do servidor.

## Fora do âmbito

- Avaliações e reputação.
- Denúncias e moderação de conteúdos.
- Eventos guardados/favoritos.
- Categorias geríveis (lista fixa no MVP).
- Exportação CSV e relatórios avançados.
- Histórico de ações administrativas.
- Check-in/check-out na app.
- Upload de fotografia.
- Notificações push e iOS.
- API como serviço separado (no MVP é o módulo `api` do Yii2).

Ver [Requisitos](requisitos.md), [User Stories](user-stories.md) e [Roadmap](roadmap.md).

# Roadmap

## Concluído

- [x] Esqueleto Yii2 Advanced (frontend, backend, consola e common)
- [x] 11 migrações do projeto (7 tabelas de domínio + `user` + colunas do núcleo + candidatura por turno + colunas de UI) e 4 migrações de RBAC
- [x] RBAC: 3 roles, 12 permissões e a regra `eventOwner` no código
- [x] RBAC: só a empresa cria/gere eventos; `accessMobile` só no voluntário; permissões de conteúdo/sistema reservadas
- [x] Colunas do núcleo: `shift.capacity`, `suspended_until`, `last_login_at`, `description`, `website`, `location`
- [x] Candidatura por turno: `application.shift_id` e índice único `(user_id, shift_id)`
- [x] Colunas de UI: `shift.name`, `user_details.suspension_reason` e `notification.read_at`
- [x] Aliases do Apache para `/vollink` e `/vollink-admin`
- [x] Documentação, diagrama de classes e revisão do documento de especificação
- [x] Templates de interface: back-office em AdminLTE 3 e front-office em HTML e CSS, com as páginas dos mockups e dados de exemplo
- [x] US10 — registo de conta de empresa: tipo de conta, `user_details`, estado `PENDING`, email de confirmação e login bloqueado até aprovação
- [x] Modelo `UserDetails` e login por email ou username
- [x] Verificação de email a distinguir voluntário (fica ativo) de empresa (fica pendente)
- [x] Modelos ActiveRecord do domínio: `UserDetails`, `Event`, `Shift`, `Application`, `Attendance` e `Notification`
- [x] Relações nos dois sentidos, constantes de estado e timestamps nos modelos
- [x] Validações cruzadas: turno dentro do evento, candidatura no evento do turno e presença no turno da candidatura

!!! info "RBAC sincronizado"
    `php yii rbac/init` foi executado e `php yii rbac/list` confirma as permissões: a empresa tem as permissões de evento; o voluntário tem `applicationSubmit` e `accessMobile`; o `sysAdmin` tem a moderação.

## Próximos passos

A linha 1 já está concluída; as restantes continuam por fazer.

| # | Branch | Entrega |
|---|---|---|
| 1 | `feat/models` | ✅ Concluído — `UserDetails`, `Event`, `Shift`, `Application`, `Attendance` e `Notification` |
| 2 | `feat/auth` | Restante autenticação: recuperação/alteração de palavra-passe e perfil (registo, verificação de email, login por email e empresa pendente já implementados na US10) |
| 3 | `feat/events` | CRUD de eventos e turnos pela empresa, estados e controlo de capacidade |
| 4 | `feat/company` | Perfil público da empresa (descrição, website, localização, selo) e dashboard de impacto |
| 5 | `feat/applications` | Candidaturas por turno, cancelamento, aprovação/rejeição e notificações |
| 6 | `feat/attendance` | Registo de horas, confirmação de presenças e histórico |
| 7 | `feat/backoffice` | Dashboard, gestão de utilizadores, validação de empresas e moderação |
| 8 | `feat/api` | Módulo `api/` REST, autenticação por token e endpoints do Android |
| 9 | `feat/android` | 9 ecrãs, Volley, SQLite, SharedPreferences e configuração do servidor |
| 10 | `test/docs` | Testes dos fluxos principais e atualização da documentação |

Cada linha corresponde a **uma branch** e a **um PR**.

## Lacunas conhecidas

### Funcionalidades

- Recuperação e alteração de palavra-passe.
- Aprovação/rejeição de empresas no back-office (US8); a US10 já cria a conta em `PENDING`.
- CRUD de eventos, turnos, candidaturas e presenças.
- Notificações e envio de email.
- Relatório simples do evento.
- Perfil público da empresa.
- Dashboard da empresa.
- Back-office.
- Ligar as páginas de interface aos modelos: substituir as chamadas a `MockData` por queries e acrescentar o controlo de acesso do front-office.
- API REST e app Android.

Ver [Requisitos](requisitos.md) para o âmbito detalhado.

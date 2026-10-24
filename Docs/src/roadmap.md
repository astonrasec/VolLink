# Roadmap

## Concluído

- [x] Esqueleto Yii2 Advanced (frontend, backend, consola e common)
- [x] 11 migrações do projeto (7 tabelas de domínio + `user` + colunas do Modelo A + candidatura por turno + colunas de UI) e 4 migrações de RBAC
- [x] RBAC: 3 roles, 12 permissões e a regra `eventOwner` no código
- [x] RBAC Modelo A: só a empresa cria/gere eventos; `accessMobile` só no voluntário; permissões de conteúdo/sistema reservadas
- [x] Colunas do núcleo do Modelo A: `shift.capacity`, `suspended_until`, `last_login_at`, `description`, `website`, `location`
- [x] Candidatura por turno: `application.shift_id` e índice único `(user_id, shift_id)`
- [x] Colunas de UI: `shift.name`, `user_details.suspension_reason` e `notification.read_at`
- [x] Aliases do Apache para `/vollink` e `/vollink-admin`
- [x] Documentação, diagrama de classes e revisão do documento de especificação

!!! info "RBAC sincronizado"
    `php yii rbac/init` foi executado e `php yii rbac/list` confirma os links do Modelo A: a empresa tem as permissões de evento; o voluntário tem `applicationSubmit` e `accessMobile`; o `sysAdmin` tem a moderação.

## Próximos passos

| # | Branch | Entrega |
|---|---|---|
| 1 | `feat/models` | ActiveRecord de `UserDetails`, `Event`, `Shift`, `Application`, `Attendance` e `Notification` |
| 2 | `feat/auth` | Registo, verificação de email, login por email, recuperação de palavra-passe e empresa pendente |
| 3 | `feat/events` | CRUD de eventos e turnos pela empresa, estados e controlo de capacidade |
| 4 | `feat/company` | Perfil público da empresa (descrição, website, localização, selo) e dashboard de impacto |
| 5 | `feat/applications` | Candidaturas por turno, cancelamento, aprovação/rejeição e notificações |
| 6 | `feat/attendance` | Registo de horas, confirmação de presenças e histórico |
| 7 | `feat/backoffice` | Dashboard, gestão de utilizadores, validação de empresas e moderação |
| 8 | `feat/api` | Módulo `api/` REST, autenticação por token e endpoints do Android |
| 9 | `feat/android` | 9 ecrãs, Volley, SQLite, SharedPreferences e configuração do servidor |
| 10 | `test/docs` | Testes dos fluxos principais e atualização da documentação |

Cada linha corresponde a **uma branch** e a **um PR**.

## Fase 2 (fora do âmbito)

- Avaliações e reputação dos organizadores.
- Denúncias e moderação de conteúdos.
- Eventos guardados/favoritos.
- Gestão de categorias (no MVP, lista fixa).
- Exportação CSV e relatórios avançados.
- Histórico de ações administrativas.
- Check-in/check-out na app Android.
- Upload de fotografia.
- Notificações push e suporte iOS.
- API como serviço separado (no MVP, módulo `api` do Yii2).

## Decisões pendentes

1. **`contentModerate` / `systemManage`** — permissões reservadas, sem entidade/serviço no MVP. Remover do uso ou manter como reservadas.
2. **`post`/conteúdos** — não existe entidade `post`; a moderação de conteúdos não faz parte do MVP.
3. **Categorias** — lista fixa em código (`AMBIENTE`, `EDUCACAO`, `SAUDE`, `SOCIAL`, `CULTURA`, `DESPORTO`, `OUTRO`) em vez de tabela `category`.
4. **Turnos** — a gestão de turnos fica dentro de `eventManage`; não há permissão `shiftManage` no MVP.
5. **Validação de empresas** — fica dentro de `userModerate`; não há permissão `companyValidate` no MVP.

## Lacunas conhecidas

### Funcionalidades

- Modelos ActiveRecord (só existem `User` e `LoginForm`).
- Registo e verificação de email.
- Login por email (atualmente o template usa `username`).
- CRUD de eventos, turnos, candidaturas e presenças.
- Notificações e envio de email.
- Relatório simples do evento.
- Perfil público da empresa.
- Dashboard da empresa.
- Back-office.
- API REST e app Android.

Ver [Requisitos](requisitos.md) para o âmbito detalhado.

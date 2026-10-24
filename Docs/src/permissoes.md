# Permissões

O controlo de acessos usa **RBAC** (`yii\rbac\DbManager`), com **3 roles**, **12 permissões** e **1 regra**. No MVP, **10 permissões estão ativas** e **2 estão reservadas**.

## Roles

| Role | Descrição |
|---|---|
| `sysAdmin` | Administrador do sistema. Atua no back-office. |
| `voluntario` | Voluntário. Usa o website e a app Android; candidata-se a eventos e acompanha horas. **Não cria nem gere eventos.** |
| `empresa` | Empresa. É a única role que cria e gere eventos; **não se candidata**. Tem perfil público e dashboard. Não usa a app Android no MVP. |

!!! info "Coordenador"
    Não existe um role `coordenador`. Quem cria um evento passa a poder geri-lo através da regra `eventOwner`, aplicada a três permissões. O SysAdmin não usa esta regra — usa `moderateEvent`.

## Permissões

| Permissão | Descrição | `sysAdmin` | `voluntario` | `empresa` | Estado |
|---|---|:---:|:---:|:---:|---|
| `accessBackoffice` | Aceder à área de gestão | ✅ | ❌ | ❌ | Ativa |
| `accessFrontoffice` | Aceder ao front-office autenticado | ✅ | ✅ | ✅ | Ativa |
| `accessMobile` | Aceder à API da app Android | ❌ | ✅ | ❌ | Ativa (só voluntário) |
| `eventCreate` | Criar um evento (nasce em `DRAFT`) | ❌ | ❌ | ✅ | Ativa |
| `eventManage` | Gerir os eventos que criou, incluindo turnos | ❌ | ❌ | ✅ | Ativa |
| `moderateEvent` | Back-office: gerir qualquer evento | ✅ | ❌ | ❌ | Ativa |
| `applicationSubmit` | Candidatar-se a um turno | ❌ | ✅ | ❌ | Ativa |
| `applicationDecide` | Aprovar ou rejeitar candidaturas dos seus eventos | ❌ | ❌ | ✅ | Ativa |
| `presenceConfirm` | Registar horas e confirmar presenças | ❌ | ❌ | ✅ | Ativa |
| `userModerate` | Gerir contas e validar empresas | ✅ | ❌ | ❌ | Ativa |
| `contentModerate` | Moderar conteúdos e publicações | — | — | — | Reservada |
| `systemManage` | Gerir as configurações do sistema | — | — | — | Reservada |

!!! info "Regras aplicadas"
    O `RbacController` aplica estas regras: a empresa é a única que cria e gere eventos; o voluntário só tem `applicationSubmit` e `accessMobile`. `contentModerate` e `systemManage` ficam reservadas, sem roles associadas.

    `php yii rbac/init` sincroniza os roles e permissões com a base de dados.

!!! info "Permissões reservadas"
    `contentModerate` e `systemManage` existem no RBAC, mas não têm entidade/serviço no MVP (não há `post` nem tabela de configurações). Estão reservadas e não devem ser usadas em controladores.

## A regra `eventOwner`

Três permissões só fazem sentido no contexto de um evento concreto:

- `eventManage`
- `applicationDecide`
- `presenceConfirm`

Todas usam a regra `common\rbac\EventOwnerRule`, que devolve `true` apenas quando o utilizador autenticado é o `creator_id` do evento. O criador é sempre um utilizador da empresa:

```php
return $creatorId !== null && (int) $creatorId === (int) $user;
```

Chamada típica:

```php
if (Yii::$app->user->can('eventManage', ['event' => $event])) {
    // o utilizador pode gerir este evento
}
```

O SysAdmin **não** recebe estas permissões. Para intervir em qualquer evento usa `moderateEvent`.

## Comandos

```powershell
# criar/atualizar roles, permissões e regras (idempotente)
php yii rbac/init

# listar roles, permissões e ligações
php yii rbac/list

# atribuir um role a um utilizador
php yii rbac/assign diogo@exemplo.pt sysAdmin

# remover um role
php yii rbac/revoke diogo@exemplo.pt sysAdmin

# verificar uma permissão (com evento opcional)
php yii rbac/check diogo@exemplo.pt eventManage 1
```

## Regras de ouro

1. **Nunca** verificar roles diretamente (`user->can('sysAdmin')`). Verifica sempre **permissões**.
2. O front-office e o back-office são separados por `accessFrontoffice` e `accessBackoffice`.
3. Toda a gestão de um evento passa pela regra `eventOwner` — exceto no back-office, que usa `moderateEvent`.
4. Uma permissão nova implica atualizar `console/controllers/RbacController.php` e correr `php yii rbac/init`.
5. A app Android usa `accessMobile`; no MVP, só o role `voluntario` a deve ter.

# VolLink

**Plataforma de Gestão de Voluntariado**

O VolLink liga voluntários, empresas e organizações a eventos de voluntariado. Permite criar e divulgar eventos, organizar turnos, receber candidaturas, confirmar presenças e contabilizar horas — com regras de acesso distintas para cada perfil.

## Duas interfaces

| | Website | Android |
|---|---|---|
| **Para quem** | Visitantes, voluntários, empresas, coordenadores e SysAdmin | Voluntários |
| **O que faz** | Front-office completo + back-office | Consultar eventos, candidatar-se, cancelar, ver histórico/horas, notificações e perfil |
| **O que não faz** | — | Criar eventos, gerir turnos, aprovar candidaturas, back-office, empresa |
| **Stack** | PHP 8.2 + Yii2 + MySQL/MariaDB + Bootstrap 5 | Android Studio + Volley + SQLite + SharedPreferences |
| **Consome** | Base de dados via Yii2 | API REST do VolLink |

## Funcionalidades

### Website

**Front-office**

- Registo de voluntários e de empresas, com verificação de email
- Login por email e recuperação/alteração de palavra-passe
- Perfil: nome, competências e data de nascimento
- Criação e gestão de eventos pela **empresa** (rascunho → publicado → concluído ou cancelado)
- Turnos por evento, com horários e vagas
- Candidaturas a turnos — apenas por voluntários
- Aprovação e rejeição de candidaturas pelo coordenador
- Registo e confirmação de presenças e horas pelo coordenador
- Histórico e total de horas do voluntário
- Relatório simples do evento
- Perfil público da empresa e dashboard de impacto
- Notificações in-app (lidas/não lidas) e email

**Back-office**

- Dashboard com contagens de utilizadores, eventos, candidaturas e horas
- Moderação de eventos
- Gestão de utilizadores e suspensões
- Validação de empresas

### Android

- Login e registo de voluntário
- Feed de eventos com filtros por categoria e localização
- Detalhe do evento (turnos, vagas e organizador)
- Candidatura e cancelamento
- Minhas candidaturas
- Perfil, histórico e total de horas
- Notificações in-app
- Menu de configuração do servidor

> **Âmbito Android:** a app é só para voluntários e cumpre os requisitos técnicos do AMSI — 6 a 10 atividades, 4 CRUD sobre o web service, 2 atividades com input, 2 dinâmicas, 1 master/detail, Volley, SharedPreferences, SQLite e menu de configuração do servidor.

## Perfis

| Perfil | O que pode fazer |
|---|---|
| **Visitante** | Consultar os eventos publicados, sem conta |
| **Voluntário** | Website + Android: candidatar-se, cancelar, ver histórico/horas. **Não cria nem gere eventos.** |
| **Empresa** | Website: criar e gerir eventos, decidir candidaturas, confirmar presenças, editar o perfil público e consultar o dashboard de impacto; **não** se candidata. |
| **Coordenador** | Utilizador da empresa que criou o evento — gere apenas os seus eventos. **Não é uma role RBAC.** |
| **SysAdmin** | Back-office: gere utilizadores, valida empresas, modera eventos e consulta estatísticas. |

> O **Coordenador** é o resultado da regra `eventOwner`, aplicada ao utilizador da empresa que criou o evento. O SysAdmin usa a permissão `moderateEvent`, não a regra de propriedade.

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

## Instalação

```powershell
cd VolLink_Web
composer install
php init
php yii migrate
php yii migrate --migrationPath=@yii/rbac/migrations
php yii rbac/init
```

Antes das migrações, configura a ligação à base de dados em `VolLink_Web/common/config/main-local.php`:

```php
'db' => [
    'class' => \yii\db\Connection::class,
    'dsn' => 'mysql:host=localhost;dbname=vollink_db;charset=utf8mb4',
    'username' => 'root',
    'password' => '',
],
```

O `main-local.php` **não é versionado**: as credenciais ficam apenas na tua máquina. Os templates em `VolLink_Web/environments/` têm valores neutros.

Os passos completos, incluindo a configuração do Apache e da API, estão na [documentação](Docs/src/instalacao.md).

## Estrutura

```text
VolLink/
├── .github/workflows/     Deploy da documentação (GitHub Pages)
├── Docs/                  Documentação (MkDocs + Material)
│   ├── src/               Páginas em Markdown
│   ├── requirements.txt
│   └── mkdocs.yml
├── VolLink_Web/           Aplicação Yii2 Advanced
│   ├── backend/           Back-office (sysAdmin)
│   ├── common/            Modelos, configuração e RBAC
│   ├── console/           Migrações e comandos
│   ├── environments/      Templates de configuração
│   └── frontend/          Front-office
└── README.md
```

A API REST será criada em `VolLink_Web/api/` e a app Android em `android/` (ambas planeadas); a Android pode ficar num repositório próprio, com os docentes adicionados.

## Documentação

O site está publicado no GitHub Pages: <https://astonrasec.github.io/VolLink/>

Em local, é servido pelo **Apache** a partir de `Docs/site`:

- <http://127.0.0.1:8000>
- <http://localhost/vollink-docs>

Para atualizar depois de editar Markdown:

```powershell
cd Docs
pip install -r requirements.txt
mkdocs build
```

O Apache serve os ficheiros novos imediatamente; não é preciso reiniciar.

Para pré-visualizar com live reload, usa outra porta (a 8000 está ocupada pelo Apache):

```powershell
cd Docs
mkdocs serve -a 127.0.0.1:8001
```

O resultado do build fica em `Docs/site/` (não versionado).

O deploy é automático: cada push para `main` que altere `Docs/` reconstrói e publica o site através do workflow `.github/workflows/docs.yml`. Também pode ser executado manualmente em **Actions → Docs → Run workflow**.

Páginas principais:

- [Funcionalidades Web](Docs/src/funcionalidades-web.md)
- [Funcionalidades Android](Docs/src/funcionalidades-android.md)
- [Requisitos](Docs/src/requisitos.md)
- [User Stories](Docs/src/user-stories.md)
- [Mockups](Docs/src/mockups.md)
- [Arquitetura](Docs/src/arquitetura.md)
- [Base de dados](Docs/src/base-de-dados.md)
- [Permissões](Docs/src/permissoes.md)

## Estado do projeto

| Área | Estado |
|---|---|
| Esqueleto Yii2 Advanced | ✅ |
| Documentação e mockups | ✅ MkDocs + Material, 33 imagens, publicado no GitHub Pages |
| Esquema da base de dados | ✅ 11 migrações do projeto + 4 de RBAC; 12 tabelas, colunas do Modelo A, candidatura por turno e colunas de UI |
| RBAC | ✅ 3 roles, 12 permissões, regra `eventOwner` e Modelo A; executar `rbac/init` para sincronizar a BD |
| URL amigáveis | ⏳ por ativar |
| Modelos ActiveRecord | ⏳ por criar (só existem `User` e `LoginForm`) |
| Autenticação completa | ⏳ por criar |
| CRUD de eventos e turnos | ⏳ por criar |
| Candidaturas, presenças e notificações | ⏳ por criar |
| Relatório do evento | ⏳ por criar |
| API REST | ⏳ por criar |
| App Android | ⏳ por criar |

> **Modelo A:** o RBAC já está alinhado — só a empresa cria e gere eventos; o voluntário participa. Se a base de dados já tinha o RBAC antigo, voltar a executar `php yii rbac/init` para remover os links antigos.

Consultar o [roadmap](Docs/src/roadmap.md) para os próximos passos e o âmbito da fase 2.

## Autor

**Diogo Almeida** — [@astonrasec](https://github.com/astonrasec)

Projeto académico desenvolvido no IPLeiria.

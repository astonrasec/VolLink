# Instalação

Guia para colocar o VolLink a correr numa máquina local (Windows + XAMPP).

## Requisitos

| Ferramenta | Versão mínima |
|---|---|
| PHP | 8.2 (com `pdo_mysql`, `mbstring`, `intl`, `openssl` e `fileinfo`) |
| Composer | 2.x |
| MySQL / MariaDB | 5.7 / 10.4 |
| Apache | 2.4 com `mod_rewrite` |
| Python (opcional) | 3.9 — apenas para o site de documentação |

!!! tip "XAMPP"
    O XAMPP 8.2 já traz tudo o que é necessário. Basta iniciar o **Apache** e o **MySQL** no painel de controlo.

## 1. Obter o código

```powershell
git clone https://github.com/astonrasec/VolLink.git
cd VolLink
```

## 2. Instalar as dependências

```powershell
cd VolLink_Web
composer install
```

## 3. Inicializar os ambientes

```powershell
php init
```

Escolhe **Development** (`0`) e confirma. Este passo cria os ficheiros `*-local.php` a partir dos templates em `VolLink_Web/environments/`.

!!! warning "Ficheiros locais"
    Os ficheiros `main-local.php` e `params-local.php` contêm credenciais e **não são versionados**. Nunca os forces para o repositório.

## 4. Criar a base de dados

```sql
CREATE DATABASE vollink_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

Pelo phpMyAdmin, ou pela linha de comandos:

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -uroot -e "CREATE DATABASE IF NOT EXISTS vollink_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## 5. Configurar a ligação

Edita `VolLink_Web/common/config/main-local.php` e garante o bloco `db`:

```php
'db' => [
    'class' => \yii\db\Connection::class,
    'dsn' => 'mysql:host=localhost;dbname=vollink_db;charset=utf8mb4',
    'username' => 'root',
    'password' => '',
    'tablePrefix' => '',
],
```

## 6. Criar as tabelas

```powershell
php yii migrate
php yii migrate --migrationPath=@yii/rbac/migrations
```

O primeiro comando cria as tabelas do domínio, as colunas do núcleo, a candidatura por turno e as colunas de UI (11 migrações do projeto, incluindo a tabela `user` do template). O segundo cria as 4 tabelas de RBAC (`auth_*`), que vivem no `vendor`.

No total ficam **16 registos** na tabela `migration` (11 do projeto + 4 do RBAC + `m000000_000000_base`).

## 7. Criar os roles e as permissões

```powershell
php yii rbac/init
php yii rbac/list
```

Resultado esperado:

```text
OK - 3 roles, 12 permissoes
```

O comando é idempotente: pode ser executado várias vezes sem duplicar registos.

## 8. Configurar o Apache

### 8.1 Alias

No fim de `C:\xampp\apache\conf\httpd.conf`, adiciona os dois blocos abaixo. **O backend tem de vir primeiro**, para o Apache não confundir `/vollink` com `/vollink-admin`.

```apache
Alias /vollink-admin "C:/Git/VolLink/VolLink_Web/backend/web"
<Directory "C:/Git/VolLink/VolLink_Web/backend/web">
    AllowOverride All
    Require all granted
</Directory>

Alias /vollink "C:/Git/VolLink/VolLink_Web/frontend/web"
<Directory "C:/Git/VolLink/VolLink_Web/frontend/web">
    AllowOverride All
    Require all granted
</Directory>
```

Ajusta os caminhos se o projeto estiver noutra pasta. Depois reinicia o Apache.

### 8.2 URLs amigáveis

Ativa o `urlManager` em `VolLink_Web/common/config/main.php`, dentro de `components`:

```php
'urlManager' => [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
],
```

Cria o ficheiro `.htaccess` em `frontend/web/` **e** em `backend/web/`:

```apache
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php
```

### 8.3 Sem Apache

Para testes rápidos, o servidor embutido do PHP também serve:

```powershell
cd VolLink_Web
php -S localhost:8080 -t frontend/web
```

Neste caso não há URLs amigáveis: o acesso é feito por `index.php?r=...`.

## 9. Verificar

| Aplicação | URL |
|---|---|
| Front-office | <http://localhost/vollink> |
| Back-office | <http://localhost/vollink-admin> |

Para dar acesso de administrador a uma conta:

```powershell
php yii rbac/assign utilizador@exemplo.pt sysAdmin
```

## 10. API REST (planeada)

O módulo `api/` ainda não existe no repositório. Quando for criado, segue o mesmo padrão do frontend/backend:

```apache
Alias /vollink-api "C:/Git/VolLink/VolLink_Web/api/web"
<Directory "C:/Git/VolLink/VolLink_Web/api/web">
    AllowOverride All
    Require all granted
</Directory>
```

- A API fica disponível em `http://localhost/vollink-api`.
- A autenticação é por token: `POST /api/auth/login` devolve o `auth_key` do utilizador.
- A app Android envia `Authorization: Bearer <token>`.
- A permissão `accessMobile` deve ficar apenas no role `voluntario` no MVP.

## 11. App Android (planeada)

Pré-requisitos:

| Ferramenta | Versão |
|---|---|
| Android Studio | versão atual |
| Android SDK | mínimo Android 8 (API 26) |
| Volley | biblioteca de rede obrigatória (AMSI) |
| SQLite | cache local (Room não conta para o AMSI) |
| SharedPreferences | endereço do servidor e token |

Passos:

1. Abrir o projeto Android em Android Studio.
2. Configurar o **endereço do servidor** no ecrã de Configurações da app (ex.: `http://192.168.1.10/vollink-api`).
3. Testar o login com uma conta de voluntário.
4. Confirmar que o feed, o detalhe, a candidatura e o cancelamento funcionam.

!!! note "Estado"
    A app Android ainda não está no repositório. O âmbito e os ecrãs estão em [Funcionalidades Android](funcionalidades-android.md).

## Problemas comuns

| Sintoma | Causa provável | Solução |
|---|---|---|
| `404` em `/vollink/site/login` | `mod_rewrite` desligado ou `AllowOverride None` | Ativar o módulo e usar `AllowOverride All` |
| Página em branco | Erro PHP | Ver `VolLink_Web/runtime/logs/app.log` |
| `could not find driver` | `pdo_mysql` desligado | Ativar no `php.ini` e reiniciar o Apache |
| `Unknown database 'vollink_db'` | Base de dados não criada | Executar o passo 4 |
| MySQL não arranca no XAMPP | Serviço preso | Carregar em **Stop** e depois **Start** |

## Documentação

O site é servido pelo **Apache** a partir de `Docs/site`, sem precisares de manter o `mkdocs serve` aberto:

- <http://127.0.0.1:8000>
- <http://localhost/vollink-docs>

O `httpd.conf` tem um `Listen 8000`, um `<VirtualHost *:8000>` com `DocumentRoot "C:/Git/VolLink/Docs/site"` e o alias `/vollink-docs`.

Para atualizar o site depois de editares Markdown:

```powershell
cd Docs
mkdocs build
```

O Apache serve os ficheiros novos imediatamente; **não é preciso reiniciar**.

Para pré-visualizar com live reload, usa outra porta (a 8000 está ocupada pelo Apache):

```powershell
cd Docs
mkdocs serve -a 127.0.0.1:8001
```

Instalação das dependências do MkDocs:

```powershell
python -m pip install -r Docs/requirements.txt
```

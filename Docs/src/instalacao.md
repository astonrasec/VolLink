# Instalação

Guia para colocar o VolLink a correr em **Windows** ou **Linux** e ficar com o ambiente pronto para trabalhar.

!!! info "Dois caminhos"
    - **Rápido (recomendado):** servidor embutido do PHP. Não precisas de Apache.
    - **Apache:** configuração com `/vollink` e `/vollink-admin`, mais próxima de produção.

## 0. Antes de começar

- **Acesso ao repositório:** o `astonrasec/VolLink` é privado. Pede ao Diogo para te adicionar em **GitHub → Settings → Collaborators → Add people** (role **Write**) e aceita o convite por email.
- **Git**, **PHP 8.2+**, **Composer 2** e **MySQL/MariaDB**.
- **Python 3.9+** apenas se fores editar a documentação.

!!! tip "Autenticação no GitHub"
    No Linux, o `git clone` por HTTPS pede um **Personal Access Token** (scope `repo`) no lugar da palavra-passe. Em alternativa, configura uma chave SSH.

!!! note "Branch de trabalho"
    O trabalho atual está em `feat/mvp-foundation` até o PR ser merged. Se depois do clone não vires `Docs/src` nem as migrações novas, muda para essa branch:

    ```bash
    git switch feat/mvp-foundation
    ```

    Depois do merge, usa `main`.

## 1. Obter o código

=== "Windows"

    ```powershell
    git clone https://github.com/astonrasec/VolLink.git
    cd VolLink
    ```

=== "Linux"

    ```bash
    git clone https://github.com/astonrasec/VolLink.git
    cd VolLink
    ```

## 2. Instalar as ferramentas

### Windows — XAMPP

1. Instala o [XAMPP 8.2](https://www.apachefriends.org/) (PHP 8.2 + MySQL/MariaDB + Apache).
2. Adiciona `C:\xampp\php` ao **PATH** do Windows e abre um terminal novo.
3. Instala o [Composer](https://getcomposer.org/download/).
4. Confirma:

```powershell
php -v
composer --version
php -m | Select-String "pdo_mysql|mbstring|intl|openssl|fileinfo"
```

Se faltar alguma extensão, descomenta-a no `C:\xampp\php\php.ini` e reinicia o Apache.

### Linux — Ubuntu/Debian

```bash
sudo apt update
sudo apt install -y git php php-cli php-mysql php-mbstring php-intl php-xml php-curl php-zip unzip composer python3 python3-venv python3-pip
sudo apt install -y mysql-server
sudo systemctl enable --now mysql
```

Se usares MariaDB, troca `mysql-server` por `mariadb-server` e o serviço por `mariadb`.

Confirma:

```bash
php -v
composer --version
php -m | grep -E "pdo_mysql|mbstring|intl|openssl|fileinfo"
```

!!! tip "PHP 8.2+"
    O Ubuntu 24.04 instala PHP 8.3, que é compatível. O projeto exige PHP 8.2 ou superior.

## 3. Instalar as dependências PHP

```powershell
cd VolLink_Web
composer install
```

O `composer.lock` está versionado, por isso todos instalam as mesmas versões.

## 4. Inicializar os ambientes

```powershell
php init --env=Development --overwrite=n
```

Este passo cria:

- `common/config/main-local.php`, `frontend/config/main-local.php`, etc.;
- `frontend/web/index.php` e `backend/web/index.php`;
- as pastas `runtime/` e `web/assets/`.

!!! warning "Ficheiros locais"
    Os `*-local.php` contêm credenciais e **não são versionados**. Nunca os forces para o repositório.

## 5. Criar a base de dados

### Windows — XAMPP

Inicia o **MySQL** no painel do XAMPP e executa:

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS vollink_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE USER IF NOT EXISTS 'vollink'@'localhost' IDENTIFIED BY 'vollink123'; GRANT ALL PRIVILEGES ON vollink_db.* TO 'vollink'@'localhost'; FLUSH PRIVILEGES;"
```

### Linux

```bash
sudo mysql -e "CREATE DATABASE IF NOT EXISTS vollink_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'vollink'@'localhost' IDENTIFIED BY 'vollink123'; GRANT ALL PRIVILEGES ON vollink_db.* TO 'vollink'@'localhost'; FLUSH PRIVILEGES;"
```

!!! note "Utilizador dedicado"
    O utilizador `vollink` / `vollink123` é só para desenvolvimento local. Podes usar `root` sem palavra-passe no XAMPP, mas o utilizador dedicado funciona igual nos dois sistemas.

## 6. Configurar a ligação à base de dados

Edita `VolLink_Web/common/config/main-local.php` e garante o bloco `db`:

```php
'db' => [
    'class' => \yii\db\Connection::class,
    'dsn' => 'mysql:host=localhost;dbname=vollink_db;charset=utf8mb4',
    'username' => 'vollink',
    'password' => 'vollink123',
    'tablePrefix' => '',
],
```

Se optares por `root` no XAMPP, usa `'username' => 'root'` e `'password' => ''`.

## 7. Criar as tabelas e o RBAC

```powershell
php yii migrate --interactive=0
php yii migrate --migrationPath=@yii/rbac/migrations --interactive=0
php yii rbac/init
```

- O primeiro comando cria as tabelas do domínio e as colunas do núcleo.
- O segundo cria as 4 tabelas `auth_*`.
- O terceiro cria os **3 roles** e as **12 permissões**.

Verifica:

```powershell
php yii migrate/history
php yii rbac/list
```

Deves ver **16 registos** na tabela `migration` e **3 roles** com as **12 permissões**.

## 8. Correr a aplicação

### Opção A — servidor embutido (recomendado)

Abre **dois terminais** em `VolLink_Web`.

Terminal 1 — front-office:

```powershell
php yii serve 127.0.0.1:8080 --docroot=frontend/web
```

Terminal 2 — back-office:

```powershell
php yii serve 127.0.0.1:8081 --docroot=backend/web
```

| Aplicação | URL |
|---|---|
| Front-office | <http://127.0.0.1:8080/> |
| Login do front-office | <http://127.0.0.1:8080/index.php?r=site%2Flogin> |
| Back-office | <http://127.0.0.1:8081/> |
| Login do back-office | <http://127.0.0.1:8081/index.php?r=site%2Flogin> |

Os comandos são iguais em Windows e Linux. Para parar, usa `Ctrl+C`.

!!! note "URLs amigáveis"
    Por omissão o `urlManager` está comentado, por isso os endereços usam `index.php?r=...`. As páginas e os links gerados pelo Yii funcionam normalmente.

### Opção B — Apache

#### Windows — XAMPP

No fim de `C:\xampp\apache\conf\httpd.conf`, adiciona os dois blocos. **O backend tem de vir primeiro**, para o Apache não confundir `/vollink` com `/vollink-admin`.

```apache
Alias /vollink-admin "C:/VolLink/VolLink_Web/backend/web"
<Directory "C:/VolLink/VolLink_Web/backend/web">
    AllowOverride All
    Require all granted
</Directory>

Alias /vollink "C:/VolLink/VolLink_Web/frontend/web"
<Directory "C:/VolLink/VolLink_Web/frontend/web">
    AllowOverride All
    Require all granted
</Directory>
```

- Ajusta `C:/VolLink` para a pasta onde fizeste o clone. Usa barras `/`.
- Confirma que `LoadModule rewrite_module modules/mod_rewrite.so` está descomentado.
- Reinicia o Apache.

#### Linux — Apache2

```bash
sudo apt install -y apache2 libapache2-mod-php
sudo a2enmod rewrite
sudo nano /etc/apache2/conf-available/vollink.conf
```

!!! note "Pasta do projeto"
    Os exemplos usam `/var/www/VolLink`. Se clonaste noutro sítio, move o projeto:

    ```bash
    sudo mkdir -p /var/www
    sudo mv ~/VolLink /var/www/VolLink
    sudo chown -R "$USER":www-data /var/www/VolLink
    ```

Conteúdo do ficheiro (ajusta `/var/www/VolLink` se o clone estiver noutro sítio):

```apache
Alias /vollink-admin /var/www/VolLink/VolLink_Web/backend/web
<Directory /var/www/VolLink/VolLink_Web/backend/web>
    AllowOverride All
    Require all granted
</Directory>

Alias /vollink /var/www/VolLink/VolLink_Web/frontend/web
<Directory /var/www/VolLink/VolLink_Web/frontend/web>
    AllowOverride All
    Require all granted
</Directory>
```

Ativa e recarrega:

```bash
sudo a2enconf vollink
sudo systemctl enable --now apache2
sudo systemctl reload apache2
```

Permissões para o Apache conseguir ler e escrever:

```bash
sudo chown -R "$USER":www-data /var/www/VolLink
sudo find /var/www/VolLink -type d -exec chmod 775 {} \;
sudo find /var/www/VolLink -type f -exec chmod 664 {} \;
```

| Aplicação | URL |
|---|---|
| Front-office | <http://localhost/vollink/> |
| Back-office | <http://localhost/vollink-admin/> |

??? note "Opcional: ativar URLs amigáveis"
    Só é necessário se quiseres endereços como `/vollink/site/login` em vez de `index.php?r=site%2Flogin`.

    1. Em `VolLink_Web/frontend/config/main.php` e `VolLink_Web/backend/config/main.php`, descomenta o bloco `urlManager`.
    2. Cria `.htaccess` em `frontend/web` e `backend/web`:

    === "Windows"

        ```powershell
        $rule = @"
        RewriteEngine on
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . index.php
        "@
        Set-Content VolLink_Web\frontend\web\.htaccess $rule -Encoding ascii
        Set-Content VolLink_Web\backend\web\.htaccess $rule -Encoding ascii
        ```

    === "Linux"

        ```bash
        for dir in frontend backend; do
          cat > "VolLink_Web/$dir/web/.htaccess" <<'EOF'
        RewriteEngine on
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . index.php
        EOF
        done
        ```

    3. Confirma que o Apache tem `mod_rewrite` e `AllowOverride All`.
    4. Passa a usar `/vollink/site/login` e `/vollink-admin/site/login`.

## 9. Verificar o ambiente

```powershell
php yii migrate/history
php yii rbac/list
php -l frontend/controllers/SiteController.php
```

- `migrate/history` deve listar **16 registos**.
- `rbac/list` deve mostrar `empresa`, `sysAdmin` e `voluntario`.
- Abre o front-office e o back-office nos URLs acima.

Comandos úteis depois de existir uma conta:

```powershell
php yii rbac/assign utilizador@exemplo.pt sysAdmin
php yii rbac/check utilizador@exemplo.pt accessBackoffice
```

## 10. Documentação

O site também está publicado em <https://astonrasec.github.io/VolLink/>. Para o editar localmente, abre um terminal na **raiz do repositório** e entra em `Docs`:

### Windows

```powershell
cd Docs
py -3 -m venv .venv
.\.venv\Scripts\python -m pip install --upgrade pip
.\.venv\Scripts\python -m pip install -r requirements.txt
.\.venv\Scripts\python -m mkdocs serve -a 127.0.0.1:8001
```

!!! tip "Comando `py`"
    Se o launcher `py` não existir na tua máquina, usa `python` no lugar de `py -3`.

### Linux

```bash
cd Docs
python3 -m venv .venv
source .venv/bin/activate
python -m pip install --upgrade pip
python -m pip install -r requirements.txt
python -m mkdocs serve -a 127.0.0.1:8001
```

Abre <http://127.0.0.1:8001/VolLink/>. O `site_url` do `mkdocs.yml` é `https://astonrasec.github.io/VolLink/`, por isso o servidor local usa o prefixo `/VolLink/`.

Para gerar o site estático, na pasta `Docs`:

```powershell
.\.venv\Scripts\python -m mkdocs build
```

```bash
python -m mkdocs build
```

!!! note "Deploy"
    O site de produção é atualizado automaticamente pelo workflow `.github/workflows/docs.yml` em cada push para `main`. Não é preciso fazer deploy manual.

## 11. Fluxo de trabalho

```bash
git switch main
git pull
git switch -c feat/nome-da-tarefa

# editar e testar

git add .
git commit -m "feat: descrição curta"
git push -u origin feat/nome-da-tarefa
```

- Abre um **Pull Request** para `main`.
- O merge em `main` atualiza a documentação publicada.
- **Nunca** comites `vendor/`, `*-local.php`, `Docs/site/` ou `.venv/`.

## 12. Problemas comuns

| Sintoma | Causa provável | Solução |
|---|---|---|
| `php` não é reconhecido | PHP fora do PATH | Adiciona `C:\xampp\php` ao PATH (Windows) ou instala `php-cli` (Linux) e abre um terminal novo |
| `composer` não é reconhecido | Composer não instalado | Instala o Composer e abre um terminal novo |
| `could not find driver` | `pdo_mysql` desligado | Ativa a extensão no `php.ini` / instala `php-mysql` e reinicia o Apache |
| `Unknown database 'vollink_db'` | Base de dados não criada | Repete o passo 5 |
| `Access denied for user 'vollink'` | Credenciais erradas | Confirma o utilizador e a palavra-passe no `main-local.php` |
| `No such file or directory` (MySQL) | Socket em falta no Linux | Usa `mysql:host=127.0.0.1` no DSN ou inicia o MySQL |
| `The directory is not writable` | `runtime/` ou `web/assets/` sem permissões | `php init` outra vez ou ajusta as permissões (passo 8B) |
| `404` em `/vollink/site/login` | URLs amigáveis desligadas | Usa `/vollink/index.php?r=site%2Flogin` ou ativa o bloco opcional |
| `Port 8080 already in use` | Porta ocupada | Usa `8082`/`8083` nos comandos `php yii serve` |
| `No module named mkdocs` | Venv não ativado | Usa `Docs\.venv\Scripts\python` (Windows) ou `Docs/.venv/bin/python` (Linux) |
| `Activate.ps1 cannot be loaded` | Política do PowerShell | Usa `Docs\.venv\Scripts\python` diretamente, sem ativar |
| `auth_item` não existe | RBAC migrations não correram | Executa o segundo comando do passo 7 |

## 13. API e Android

A API REST e a app Android ainda não estão no repositório. O âmbito, os ecrãs e os endpoints estão definidos em:

- [Funcionalidades Android](funcionalidades-android.md)
- [Arquitetura](arquitetura.md)
- [Roadmap](roadmap.md)

Quando forem criados, seguem os mesmos passos de instalação e configuração da base de dados.

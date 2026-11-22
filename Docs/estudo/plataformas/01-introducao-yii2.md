# Plataformas 01 — Introdução ao Yii2 (Aula 01 + Ficha 1)

> Fontes: `Aula 01 - Introdução ao Yii2.pdf`, `PlatSI-Ficha-Prática-01.pdf`.
> UC: **Plataformas de Sistemas de Informação (PLSI)**. Objetivo do projeto: ver [`00-enunciado-e-avaliacao.md`](00-enunciado-e-avaliacao.md).
> Nas fichas, os espaços em branco ("____") eram para o aluno preencher; as respostas abaixo são **nossas** (conhecimento de estudo) e estão assinaladas como tal.

---

## 1. Revisão: o ecossistema do PHP moderno

- O PHP moderno **não é só scripts soltos**: baseia-se em **Namespaces, Orientação a Objetos e Autoloading**.
- **Composer:** gere bibliotecas — carrega dependências externas sem dezenas de `require`.
- *Controladores manuais* (sem framework): manipulação direta de `$_POST` e redirecionamentos com `header()`.

### 1.1 Desenvolvimento sem framework (PHP puro) — problemas
| Problema | Descrição |
|---|---|
| Código esparguete | Mistura SQL, lógica de negócio e HTML/CSS no mesmo ficheiro |
| Segurança manual | Alto risco de **SQL Injection, XSS, CSRF** — o programador filtra tudo à mão |
| Reinvenção da roda | Rotas, sessões seguras, paginação... programadas do zero |
| Escalabilidade | Arquitetura "desmorona-se" com o crescimento |
| Falta de standards | Cada programador escreve diferente → manutenção difícil |
| Lentidão | Projetos em meses em vez de semanas |

### 1.2 Revisão MVC (o que se usou no ano passado, com Laravel)
- **Model:** lógica de negócio + acesso a dados. **View:** interface (HTML/CSS). **Controller:** recebe o pedido, invoca o Modelo e entrega os dados à Vista.
```php
public function index() {
    $questions = Question::all();                 // invoca o Modelo
    require_once 'Views/question/index.php';      // chama a Vista (dados ficam no scope)
}
```
### 1.3 Revisão ORM (Eloquent vs SQL manual)
- **ORM** (Object-Relational Mapping): tabelas → classes PHP; registos → objetos. Mapeia relações 1:N de forma orientada a objetos.
```php
class Question extends Model {
    protected $fillable = ['text'];                // segurança Mass Assignment
    public function answers() { return $this->hasMany(Answer::class); }   // 1:N
}
```

---

## 2. O que é o Yii2 ("Yes It Is!")

Framework PHP **de alta performance, baseado em componentes**, para desenvolvimento rápido de aplicações web modernas à escala.
- **Arquitetura MVC** imposta (Model = dados/lógica; View = interface; Controller).
- **Baseado em Componentes:** tudo é um objeto configurável (Request, Response, DB).
- **DRY** (*Don't Repeat Yourself*): reutilização máxima de código.

### 2.1 Vantagens
| Vantagem | Detalhe |
|---|---|
| **Performance** | Um dos frameworks PHP mais rápidos; carrega só o necessário (**lazy loading**); cache nativa. Ex.: num questionário, as respostas só são lidas da BD quando o código as pede |
| **Segurança nativa** | Defesas integradas contra **SQL injection, XSS, CSRF**, validação de cookies. Ex.: tokens gerados automaticamente no `ActiveForm` |
| **Geração de código** | Módulo **Gii**: gera Models, Controllers e Views lendo a estrutura da BD (*scaffolding*). Ex.: CRUD completo de "Produto" com 2 cliques |

### 2.2 Desvantagens / desafios
- **Curva de aprendizagem** (arquitetura rigorosa, muitos arrays de configuração, *Behaviors* e *Events*).
- **Overhead em projetos pequenos** (site estático ou script de 2 páginas).
- **Menos liberdade — efeito "caixa preta":** é obrigatório programar "à maneira do Yii2"; erros complexos são mais difíceis de perceber.

### 2.3 Comparação PHP puro vs Yii2
| Característica | PHP puro | Yii2 |
|---|---|---|
| Arquitetura | Critério do programador (frequentemente caótica) | MVC obrigatório |
| Tempo de desenvolvimento | Lento | Muito rápido (Gii e Data Widgets) |
| Segurança | Manual (alto risco) | Automática (filtros nativos, CSRF tokens) |
| Manutenção | Pesadelo para novos membros | Padronizada |
| Base de dados | PDO manual e queries em strings | **ActiveRecord** robusto |

---

## 3. Ciclo de vida do pedido (rota → controller → view)

Exemplo: `http://dominio.com/index.php?r=loja/produto&id=5`
- Yii2 entende: **Controller = Loja**, **Action = Produto**, **Parâmetro = 5**.
1. **Route** — o componente `Request` analisa o URL.
2. **Controller** — invoca `actionProduto($id)` em `LojaController`.
3. **View** — gera o HTML final, empacotado na `Response`.

Docs: <https://www.yiiframework.com/doc/guide/22.0/en>

## 4. Pré-requisitos
- **PHP** ≥ 5.4.0 (recomendado **7.4 / 8.x**): namespaces, traits, short array syntax.
- **Composer:** instala e atualiza o Yii2 e as extensões.
- **Servidor web:** Apache (com **`mod_rewrite`**) ou Nginx — necessário para *Pretty URLs* (sem `index.php?r=`).
- **Base de dados:** MySQL, PostgreSQL, SQLite, Oracle ou SQL Server (drivers PDO).

---

## 5. Ficha 1 — instalação e primeiros passos (Parte 1 de 3)

**Objetivos:** instalar/configurar a plataforma web (stack, IDE, cliente de BD (opcional), gestor de dependências, framework); automatizar instalações via gestor de dependências; reconhecer as componentes MVC do Yii2.

### 5.1 Stack web
- Escolher **LAMP, WAMP ou MAMP** (conforme o SO); atenção a 32/64 bits.
- Verificar em `http://localhost` (Root Web: `www`, `htdocs`…). Se vazia: criar `index.php` com `phpinfo();` e testar de novo.
- *(No nosso ambiente, Linux: Apache/Nginx + PHP-FPM + MariaDB/MySQL, ou servidor embutido `php yii serve`.)*

### 5.2 IDE
- **PHPStorm** (JetBrains) é de uso **obrigatório** na UC; licença educacional de 1 ano com o email da escola.

### 5.3 Composer (gestor de dependências do PHP)
Descarregar de <https://getcomposer.org/>; verificar com o comando `composer` na CLI.

Exemplo de `composer.json`:
```json
{
    "require": {
        "vendor/package": "1.3.2",
        "vendor/package2": "1.*"
    }
}
```
**Respostas de estudo (nossas) às lacunas da ficha:**
| Item | Resposta |
|---|---|
| `composer.json` | Ficheiro de definição do projeto: declara dependências (`require`, `require-dev`), autoload, scripts e metadados |
| keyword `require` | Lista os pacotes de que o projeto precisa em produção |
| Package `vendor/package` | Identificador do pacote: fornecedor/nome no Packagist |
| Versão após o package | Restrição de versão: exata (`1.3.2`), wildcard (`1.*`), `^1.3` (compatível), `~1.3`… |
| `composer.lock` | Regista as **versões exatas** efetivamente instaladas → instalações reprodutíveis (deve ir para o Git) |
| `composer.phar` | Executável PHP (arquivo) do Composer, usado quando não está instalado globalmente (`php composer.phar ...`) |

Comandos usuais: `composer update`, `composer update <package>`, `composer create-project <package> <diretoria>` (também `composer install` para instalar a partir do `.lock`).

### 5.4 Instalar o Yii2 com o Composer
1. <http://www.yiiframework.com/> → *Guide 2.0 → Getting Started → Installing Yii*.
2. **Bower e npm** (resposta nossa): gestores de pacotes de **front-end** (jQuery, Bootstrap…). O Yii2 obtém esses ativos via Composer através de um repositório/plugin de assets (*asset-packagist*).
3. Criar o projeto **basic** na raiz web:
```bash
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
```
   **Explicação (nossa):** `create-project` cria um projeto novo a partir de um pacote-modelo; `--prefer-dist` descarrega a distribuição `.zip` (mais rápido) em vez de clonar o repositório; `yiisoft/yii2-app-basic` é o template; `basic` é o nome da pasta de destino.
4. Testar: `http://localhost/basic/web`.
5. **Debugger / Profiler** (barra no canto inferior direito) — respostas de estudo:
   - **Request:** dados do pedido: **Route** (rota pedida), mapeamento da route (→ controller/action executados), parâmetros GET/POST, cabeçalhos, sessão/cookies;
   - **Logs:** mensagens de log por nível/categoria da execução;
   - **Profiling:** tempos e memória das operações (onde se perde tempo);
   - **Database:** queries SQL executadas (tempo, nº, duplicadas);
   - **Asset Bundles:** CSS/JS registados e respetivos pacotes;
   - **Mail:** emails enviados durante o pedido (com `yii2-swiftmailer`/symfonymailer).

### 5.5 Analisar e adaptar a aplicação
| Pergunta | Resposta (nossa) |
|---|---|
| Pasta das vistas | `views/` (subpastas por controller: `views/site/…`; layouts em `views/layouts/`) |
| Alterar a vista *About* | `views/site/about.php`: 3 parágrafos, cada um com título (ex.: `<h2>` + `<p>`), usando classes **Bootstrap** já integradas |
| Pasta dos controladores | `controllers/` |
| Classe do controlador único | `SiteController`, descende de **`yii\web\Controller`** |
| O que representa o controlador | Recebe o pedido, valida/prepara dados (invoca modelos) e escolhe a vista a renderizar; é o intermediário MVC |
| Métodos do `SiteController` | `behaviors()` (filtros de acesso/verbos), `actions()` (ações externas: `error`, `captcha`), `actionIndex` (home), `actionLogin`/`actionLogout`, `actionContact` (formulário + email), `actionAbout` |
| Pasta dos modelos | `models/` |
| Modelos do template basic | `LoginForm` (modelo de formulário de login), `ContactForm` (formulário de contacto, com captcha), `User` (identidade de exemplo, implementa `IdentityInterface`) |

Excerto de `views/site/about.php` com Bootstrap (exemplo nosso):
```php
<?php
use yii\helpers\Html;
$this->title = 'About';
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
    <h2>Quem somos</h2><p>…</p>
    <h2>Missão</h2><p>…</p>
    <h2>Contactos</h2><p>…</p>
</div>
```

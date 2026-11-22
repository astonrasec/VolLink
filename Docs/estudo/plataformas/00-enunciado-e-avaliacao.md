# Plataformas 00 — UC, Enunciado do Projeto e Avaliação (PLSI)

> Fontes: `Enunciado-PlatSI-2026-2027.pdf`, `01_Apresentacao_TeSP_PLSI_2026_2027.pdf`, os três PDFs de *Escalonamento das Apresentações* (17, 18 e 19-11-2026) e os `index.html` do Moodle (Anúncios, Questionários de inscrição PL1/PL2, Entrega Etapa 1 — **são só a "casca" do Moodle, sem conteúdo útil**).
> **Não incluímos** nomes/notas de outros estudantes que constem nos ficheiros de escalonamento.

---

## 1. A UC

**Plataformas de Sistemas de Informação (PLSI)** — TeSP em Programação de Sistemas de Informação, Departamento de Engenharia Informática, Universidade de Leiria e Oeste (código Moodle `EAD_2026_27_050_S1`). **7 ECTS.**

### 1.1 Docentes
| Regime | Docente | Aulas |
|---|---|---|
| Diurno | Joana Pedrosa (`joana.pedrosa@ulo.pt`) | TP1 |
| Diurno | Gonçalo Marques (`goncalo.l.marques@ulo.pt`) | PL1 e PL2 |
| Pós-laboral | Bruno Ferreira | TP e PL |
(Horários de atendimento "a definir" no slide.)

### 1.2 Avaliação
- **Não são aproveitadas notas de anos letivos anteriores.**
- **Avaliação periódica:**
  - **20 %** — Projeto **Fase 1**: entrega e **Prova Oral [PO1]**
  - **80 %** — Projeto **Fase 2**: entrega, **Prova Oral Individual [PO2]** e **Teste Prático [TP]**
  - **Nota Final = (0,2 × PO1 + 0,8 × PO2) × TP**  *(o TP multiplica — um TP fraco derruba a nota toda)*
- **Avaliação por exame:** 100 % — **Teste Prático Individual**.
- Datas (primeira entrega/PO, entrega final, PO, TP, exames): **"???" no slide — consultar o calendário de avaliação**.

### 1.3 Programa
1. **Introdução às plataformas:** 1.1 plataformas, frameworks e bibliotecas; 1.2 servidores aplicacionais e middlewares; 1.3 ferramentas de gestão de dependências.
2. **Desenvolvimento de aplicações e serviços em plataformas/frameworks:** 2.1 padrões e arquitetura aplicacional; 2.2 workflow de desenvolvimento; 2.3 ferramentas de geração de código Entidade/CRUD.
3. **Sistemas de Integração Contínua:** 3.1 qualidade em projetos de software; 3.2 importância dos testes no *quality assurance*; 3.3 **white-box vs black-box**; 3.4 especificação e realização de testes; 3.5 boas práticas de integração contínua para redução de risco.

---

## 2. Enunciado do projeto (PLSI 2026/2027)

O trabalho corresponde a **uma das componentes da UC de Projeto em PSI (Servidor Web)** e consiste no desenvolvimento do **back-office, front-office, RBAC e testes de software**. O **cenário de aplicação** é o tema do grupo na UC de Projeto → cada grupo implementa de acordo com as suas especificações (no nosso caso, **VolLink**, plataforma de gestão de voluntariado).

### 2.1 Requisitos — Fase 1
- **Instalação e configuração do RBAC** com os roles e permissões definidos para o Projeto.
- **Registo (front-office)** devidamente integrado no RBAC.
- **Gestão de utilizadores (back-office):** **CRUD completo com Soft Delete**.
- O back-office **protegido contra acessos de utilizadores com acesso exclusivo ao front-office**.
- **Templates web para as UIs:**
  - página principal do front-office e respetivos menus, **sem funcionalidades** (ex.: templates de **htmlcodex.com** ou semelhantes);
  - dashboard (página principal) do back-office e menus, **sem funcionalidades** (ex.: módulo **Yii2 Admin LTE**).

### 2.2 Requisitos — Fase 2
- **Implementação dos requisitos funcionais** de front-office e back-office.
- **Boas práticas de segurança e integridade de dados** nos controladores CRUD do back-office.
- **Autenticação e autorização** (secção 2.3).
- **Testes** (secção 2.4).

### 2.3 Autenticação e autorização
- Implementar **todas as regras** de autenticação e autorização a zonas protegidas da aplicação web.
- Usar o **ACF**, complementado pela **API RBAC**, conforme as necessidades da especificação.

### 2.4 Testes de software (mínimos)
**Testes unitários — 3 casos de teste sobre um modelo de domínio selecionado:**
1. Verificação de **restrições de campos obrigatórios**;
2. Verificação de **regras de validação estrutural** (formato, intervalos ou tipos de dados);
3. Verificação de **operações de persistência do Active Record** ou **métodos de lógica de negócio interna**.

**Teste funcional — 1 caso de teste:**
- Validação do **fluxo de submissão do formulário de criação** da entidade principal (**Create do CRUD**), aferindo o **estado final persistido** na camada de dados.

### 2.5 Entrega — Fase 2
**Zip** com: todo o código implementado (**pasta da framework**), o **SQL do projeto** e um ficheiro **README com as credenciais** necessárias para aceder ao projeto.

### 2.6 Tecnologias
- Trabalho **integralmente na framework Yii2**.
- Testes com **Codeception**.

### 2.7 Critérios de avaliação
| # | Peso | Componente |
|---|---:|---|
| 1 | **20 %** | Desenvolvimento de **Front-end** (front-office e back-office) |
| 2 | **45 %** | Desenvolvimento de **Back-end** (front-office e back-office) |
| 3 | **15 %** | **Autenticação e Autorização** |
| 4 | **10 %** | **Testes de Software** |
| 5 | **10 %** | Qualidade de implementação, otimização e inovação |

---

## 3. Calendário e logística (do que consta nos ficheiros)

| Data | Evento |
|---|---|
| 17-11-2026 (PL1), 18-11-2026 (PL2), 19-11-2026 (pós-laboral) | **Apresentações da 1.ª entrega** (20 min por grupo) |
| 19-11-2026, 19:40–20:00 | **Grupo 55 — VolLink** (turno pós-laboral) — já decorreu à data deste documento (22-11-2026) |
| Fase 2 / PO2 / TP / exames | **Datas por confirmar** (slide com "???") |

- Nos escalonamentos: 20 minutos por grupo com intervalo/margem de ajuste.
- Existem questionários de **inscrição PL1/PL2 para a 1.ª entrega** e a atividade "Entrega Trabalho Prático [Etapa 1]" no Moodle (conteúdos não exportados).

## 4. Ligações ao resto do material
| Tema do enunciado | Onde estudar |
|---|---|
| Yii2, MVC, routing | [`01`](01-introducao-yii2.md), [`02`](02-core-componentes-routing.md) |
| Migrations, Gii, ActiveForm | [`03`](03-migrations-activeform-gii.md) |
| Templates Basic/Advanced, Master-Detail | [`04`](04-basic-advanced-master-detail.md) |
| ACF + RBAC | [`05`](05-autenticacao-acf-rbac.md) |
| Widgets, Data Providers, **Soft Delete** | [`06`](06-widgets-dataprovider-softdelete.md) |
| GridView avançado, SearchModel, roles por user | [`07`](07-gridview-avancado-roles-por-user.md) |
| Aplicação ao VolLink (checklist por fase) | [`08`](08-aplicacao-ao-vollink.md) |
| Testes Codeception (**sem material nas pastas**) | [`09`](09-testes-codeception.md) |

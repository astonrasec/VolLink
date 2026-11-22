# Android 00 — UC, Enunciado do Projeto e Avaliação (AMSI)

> Fontes: `PSI_AMSI-2026_2027_Apresentacao_UC.pdf`, `Enunciado Projeto Pratico - AMSI - TeSP PSI - 2026_27.pdf`,
> `…_EpocaNormal.pdf` (adenda), `Projeto_Critérios_Avaliação_Etapa1.pdf`, escalonamentos (Etapa 1 e 1.º Teste TP),
> `PSI_AMSI_Avaliacao_Periodica_Prova_Escrita_1…pdf` e os `index.html` do Moodle (Anúncios, "Horário de Atendimento", separadores — **só casca, sem conteúdo útil**; as imagens `HorarioAtendimento*` são horários de atendimento dos docentes).
> **Não incluímos** nomes nem notas de outros estudantes (a lista da Prova Escrita 1 e os escalonamentos contêm dados pessoais).

---

## 1. A UC

**Acesso Móvel a Sistemas de Informação (AMSI)** — TeSP PSI, 2.º ano, 1.º semestre, 2026/2027 (Moodle `EAD_2026_27_014_S1`).
**6 ECTS** · 15 h TP + 60 h PL · 162 h de trabalho total.

### 1.1 Docentes
| Docente | Função | Turmas / atendimento |
|---|---|---|
| **Sónia Luz** (`sonia.luz@ipleiria.pt`) | Responsável Diurno | — |
| **David Safadinho** (`david.safadinho@ipleiria.pt`) | Responsável Pós-Laboral | — |
| **Cátia Ledesma** (`catia.ledesma@ipleiria.pt`) | Docente | Diurno PL2 e Pós-Laboral; gabinete A.GL.2-9; atendimento PL2 → 6.ª f 11h–12h, PL → 4.ª f 23h–24h |
| **Guilherme Pereira** (`guilherme.pereira@ipleiria.pt`) | Docente | Diurno PL1 e PL3; gabinete A.G.0-P (partilhado); atendimento 2.ª f 17h–18h |
(Informar previamente o docente da intenção de reunir.)

### 1.2 Objetivos
- Caracterizar as tecnologias de desenvolvimento para dispositivos móveis.
- Usar **Java** no desenvolvimento de apps **Android**.
- Desenvolver apps com **bases de dados locais** e **WebServices remotos**.

### 1.3 Programa
Evolução dos dispositivos móveis · Linguagens, plataformas e ferramentas · Estrutura das apps Android · Desenho de interfaces e interação · Gestão de atividades (navegação) · **Persistência local e acesso a WebServices**.

### 1.4 Metodologia
Presencial: **TP** (exposição + exercícios) e **PL** (resolução de problemas com ferramentas, tendo em vista o projeto em equipa). Autónoma: estudo, e-learning (Moodle), **projeto em equipa**.

### 1.5 Bibliografia
- *Android — Desenvolvimento de Aplicações com Android Studio*, Ricardo Queirós, FCA 2016
- *Beginning Android 4 Application Development*, Wei-Meng Lee, Wrox, 2011
- *Creating Android Applications: Develop and Design*, Chris Haseman, Peachpit, 2011
- *Introdução à Programação de Aplicações Android*, Pedro Inácio, 2015 (eBook)
- <https://developer.android.com/>

### 1.6 Inteligência Artificial
- **Permitida como apoio à aprendizagem.**
- **Nos elementos de avaliação NÃO é permitida**; eventual utilização **constitui fraude académica**, podendo ser exigida declaração de honra.

---

## 2. Avaliação

### 2.1 Avaliação periódica
| Componente | Peso | Notas |
|---|---:|---|
| Prova Escrita 1 (PE1) | 15 % | Sem mínimos |
| Prova Escrita 2 (PE2) | 15 % | Sem mínimos |
| Projeto Etapa 1 (P1) | 20 % | Apresentação oral em grupo; **mínimo 9,5/20** |
| Projeto Etapa 2 (P2) | 50 % | Relatório + Prova Oral em grupo + **Teste Prático Individual (TPI) [0..100 %]**; **mínimo 9,5/20** |

**Classificação = 15 % × PE1 + 15 % × PE2 + 20 % × P1 + (50 % × P2 × %TPI)**

*A nota das provas escritas é salvaguardada nas épocas seguintes do mesmo ano letivo se o estudante não se submeter a nova avaliação desse elemento.*

### 2.2 Exame normal
**Classificação = 30 % × PE + 70 % × P × %TPI** — PE sem mínimos; **P = mesmo projeto + Adenda (20 %)**; relatório + prova oral obrigatória + TPI; **mínimo 9,5/20** no projeto.

### 2.3 Recurso, Especial, Mensal, Melhoria
**Classificação = 30 % × PE + 70 % × TP** — TP com **mínimo 9,5/20**.

### 2.4 Projeto em conjunto com outras UCs
O projeto de AMSI faz-se **em conjunto com** *Plataformas de Sistemas de Informação (PLSI)* e *Serviços e Interoperabilidade de Sistemas (SIS)*; **grupo de 3** (máx. 2 se já concluíram a UC de Projeto, nas mesmas condições); propostas carecem de **aprovação dos docentes**.

---

## 3. Enunciado do projeto (regras e requisitos)

### 3.1 Geral
- Aplicação para **dispositivos móveis Android** com **acesso a webservice REST/RESTful** + apresentação intermédia (entrega da Etapa 1) + defesas (**prova oral em grupo** e **teste prático individual**).
- Temas submetidos no Moodle (**até 17-out-2026**, PDF da proposta); sujeitos a aprovação prévia; analisados face aos objetivos da UC, complexidade e requisitos.
- Grupos de **3** (se estão em Projeto); sem Projeto → individual ou 2 (e devem fazer **loja online**, tema validado pelos docentes). Outras dimensões carecem de aprovação.

### 3.2 Requisitos da aplicação móvel (⚠ checklist de avaliação)
| # | Requisito |
|---|---|
| 1 | **≥ 6 atividades** (Activity e/ou Fragment), máx. recomendado **10**; **algumas funcionalidades têm de ser com fragmentos** |
| 2 | **≥ 4 funcionalidades** que acedem ao webservice para **CRUD** (inserir, consultar, alterar, remover) |
| 3 | **Obrigatório** implementar as **4 operações CRUD** sobre alguns dos dados do webservice |
| 4 | **≥ 2 atividades** com **introdução de dados** pelo utilizador |
| 5 | **≥ 2 atividades construídas dinamicamente** com dados do webservice |
| 6 | Acesso ao webservice **obrigatoriamente com a biblioteca Volley** (outra não conta) |
| 7 | O acesso aos recursos **não pode bloquear a interface gráfica** |
| 8 | **≥ 1 relacionamento master/detail** usando os serviços da API |
| 9 | **SharedPreferences** (ficheiros locais) e **SQLite** (BD local) obrigatórios; **Room e alternativas não contam** |
| 10 | Valorizado: planeamento, regras de coordenação do grupo, boas práticas (**padrão MVC**, comentários…) |
| 11 | **Menu de configuração** para inserir os **dados do servidor** onde está a API |
| 12 | A **API tem de estar no servidor** disponibilizado pela UC de Projeto (os sem Projeto recebem acessos por email) |

### 3.3 Etapa 1 (entrega + apresentação em aula)
- Apresentação da **ideia/proposta**; **mockups** da interface; **planeamento das tarefas e etapas (milestones) por elemento do grupo** (**não são aceites links externos**).
- Apresentação **por todos os elementos**, com **diapositivos**, **máx. 10 min** (+ **5 min de discussão**); horário no Moodle.

**Critérios da Etapa 1** (ficheiro de critérios):
| Critério | Peso |
|---|---:|
| Contextualização do projeto | 10 % |
| Levantamento de requisitos (mockups da UI + requisitos) | 25 % |
| Planeamento do projeto (por elemento do grupo; ex.: tarefas, previsão de tempo) | 20 % |
| Apresentação oral | 15 % |
| Justificações (opções/ideias, dificuldades esperadas e soluções propostas) | 30 % |
Objetivos também incluem: análise e definição de requisitos; justificação de opções; apresentação das dificuldades esperadas.
Entrega no Moodle no link do turno prático; **todos participam**; documento submetido é o usado na apresentação.

### 3.4 Etapa 2 (entrega final)
- **Relatório de projeto em PDF**.
- **Projeto Android Studio pronto a compilar e executar** — **se não compilar: 0 valores**.
- **Link do GitHub** (obrigatório; **adicionar os docentes como membros** para verem os commits) e do **servidor** onde está a API.

### 3.5 Datas provisórias do enunciado
| Data | Marco |
|---|---|
| 17-10-2026 (23h59) | Proposta do projeto |
| 07-11-2026 (23h59) | Entrega da Etapa 1 — PPTX ou PDF *(o enunciado diz "07/11/**2025**": gralha; os critérios dizem 7/11/2026)* |
| 09 a 13-11-2026 | Prova Oral (Etapa 1), em aula |
| **16-01-2027 (23h59)** | **Entrega da Etapa 2** (app, relatório e apresentação) |
| 20 e 21-01-2027 | Prova Oral do projeto |
| **22-01-2027** | **Teste Prático Individual da Etapa 2** |
(Validar no calendário de avaliação definitivo.)

### 3.6 Critérios do projeto
**Etapa 1 — 20 %** (apresentação e documentação). **Etapa 2 — 50 %** (implementação e documentação; **prova oral obrigatória**; **TPI obrigatório**, valor 0–100 %, que **multiplica** a nota do projeto). Os restantes **30 %** são as duas provas escritas.

---

## 4. Adenda — Época de Exame Normal

Quem entrega em exame normal (e ainda não submeteu o tema) deve **enviar email aos docentes para validar o tema até 4-jan-2027**.

**Submissão:** ZIP **`AMSI202627_ADENDA_<NR. ALUNO>.ZIP`**, **máx. 100 MB**, no Moodle até **23:59 de 16-01-2027**, com **relatório PDF** + **projeto Android Studio compilável**.

**Funcionalidades extra da adenda** (além dos requisitos da secção 3.2):
- **Upload de imagens da galeria e da câmara** do dispositivo para o servidor web — **15 %**;
- **Criação de um PDF com dados dinâmicos enviado por email** — **5 %**.

### 4.1 Componentes do relatório (exigidos)
1. **Introdução:** contextualização da app; requisitos finais implementados; motivação.
2. **Descrição do desenvolvimento:** metodologia/procedimentos/regras (ex.: divisão de tarefas); tecnologias usadas/pesquisadas; **solução desenvolvida** — problemas/dificuldades e soluções; **justificação das soluções** (estrutura do código, divisão em classes, tipo de menus…); **funcionalidades propostas mas não implementadas**.
3. **Conclusões:** resultados, problemas por resolver, previsão de futuro (mesmo que teórico).
A estrutura **deve seguir estes pontos**.

### 4.2 Apresentação e defesa (adenda)
- Apresentação **individual**, **máx. 15 min**, em PowerPoint, **incluindo demonstração**; entregar o PPT **em PDF** até **19-01-2027**.
- **Datas:** 16-01 entrega · 19-01 PPT em PDF · 21-01 Prova Oral · 22-01 Teste Prático Individual (com adenda).
- **TPI:** projeto funcional, a compilar sem erros; **50 minutos** de **implementação de funcionalidades**, avaliada **individualmente** (0–100 %), multiplicando a nota do projeto; **tem de ser submetido no Moodle**; horário/laboratório no Moodle.

---

## 5. Factos úteis dos ficheiros de logística (sem dados pessoais)
- **1.º Teste Prático (TP)** — escalonamento diurno em **30-out-2026** (turnos 9h45–10h20 e 10h20–10h55, sala A.S.2.13) e pós-laboral em **28-out-2026** (18h20–18h55, sala A.S.0.5).
- **Apresentações da Etapa 1:** PL3 09-nov (A.S.1.2A) · Pós-Laboral 11-nov (A.S.1.2B) · PL1 13-nov (A.S.2.02) · PL2 13-nov (D.S.1.10); estudantes comparecem **5 min antes**, com a apresentação pronta; 20 min por grupo.
- **Prova Escrita 1** (periódica): 20 questões (cotação: certa 1, errada −0,25, em branco 0; total /20). Consulta da prova: contactar a Prof.ª Cátia Ledesma (20-nov 11h–12h, A.GL.2-9; pós-laboral a 18-nov após a aula PL). **PE2 — data não consta dos ficheiros.**

## 6. Ligações ao resto do material
| Tema | Onde estudar |
|---|---|
| Layouts, Activities/Intents, Fragments, RecyclerView, Menus, Temas | `02`…`07` |
| SharedPreferences | [`06`](06-menus-sharedpreferences.md) |
| Singleton + SQLite | [`09`](09-singleton-e-sqlite.md) |
| REST + Volley (**sem material nas pastas**) | [`10`](10-api-rest-volley.md) |
| Aplicação ao VolLink (ecrãs, API, checklist) | [`11`](11-aplicacao-ao-vollink.md) |

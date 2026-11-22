# Android 01 — Instalação, Computação Móvel e Introdução

> Fontes: `00_InstalacaoConfiguracaoAndroid.pdf` (Ficha 0), `01_Computacao_Movel_e_Evolucao_Tecnologias.pdf` (Cap. 1),
> `01_IntroducaoAndroid.pdf` (Ficha 1), `01_SintaxeJavaAndroid.pdf` (slides de sintaxe Java).
> UC: **Acesso Móvel a Sistemas de Informação (AMSI)** — TeSP PSI, 2.º ano, 1.º semestre, 2026/2027.

---

## 1. Ficha 0 — Instalação e configuração do Android Studio

**Objetivo:** instalar e configurar o IDE Android Studio.

### Instalação
1. Descarregar a versão recomendada em <https://developer.android.com/studio/index.html> (já inclui o **Android SDK**).
2. O assistente é simples; passos: selecionar componentes → definir pasta de instalação do IDE → escolher a pasta do *Start Menu* → terminar e iniciar o Android Studio.

### Configuração (primeiro arranque)
| Passo | O que escolher |
|---|---|
| Tipo de instalação | **Custom** |
| Componentes do SDK | selecionar logo um **dispositivo virtual (AVD / emulador)** |
| Licenças | aceitar `android-sdk-license` e `android-sdk-preview-license`, depois *Next* |
| Fim | é feito o download dos componentes; *Finish* abre o Android Studio |
| Aparência (opcional) | *Settings* → *Edit color scheme* para alterar o fundo do IDE |

> Nas salas de aula usa-se o atalho **Android Admin** para iniciar o Android Studio.

---

## 2. Capítulo 1 — Computação móvel e evolução das tecnologias

### 2.1 Mobilidade e diversidade de dispositivos
Gama variada de formatos, tamanhos e tipologias:
- Smartphones e tablets
- Redes de sensores (doméstico/exercício físico; empresarial: agricultura, ambiente, militar)
- Automóveis ligados em rede (**Android Auto**)
- Wearables e smartwatches
- Smart TVs

### 2.2 Evolução dos dispositivos (ordem do slide)
1. Telefones de uso militar
2. Primeiros telemóveis — autonomia de 30 min; primeira chamada de telemóvel para fixo em **1973**
3. Aumento da autonomia, redução de dimensões (*clamshell*)
4. Comunicação via satélite
5. PDA
6. Nokia 5000/6000
7. BlackBerry
8. iPhone
9. Android

**Tendências/futuro:** realidade aumentada, ecrãs flexíveis, projetor incorporado, ecrãs 3D e hologramas, realidade virtual, Internet das Coisas (IoT).

### 2.3 Evolução do software
- O sistema operativo evolui mais depressa que o hardware.
- Vários fabricantes, mas **Google e Apple** destacam-se como vencedores.
- **Android (Google)** tem a maior quota de mercado em número de dispositivos (slides com gráficos de quota até 2017, até 2022 e atual — fontes IDC e StatCounter).

### 2.4 O que é o Android
- Sistema operativo **open source** para dispositivos móveis.
- Plataforma fundada em **outubro de 2003 por Andy Rubin**, apoiada pela Google.
- 1.ª versão para smartphone lançada **no final de 2008** (Android 1.0: 23 set 2008).
- Inclui: SO baseado em **Kernel Linux**, um **middleware** e um conjunto de **aplicações nativas**.

### 2.5 Arquitetura do Android (camadas, de cima para baixo)
| Camada | Descrição |
|---|---|
| **Aplicações** | Já incluídas de origem; desenvolvidas em Java ou Kotlin |
| **Framework de aplicações** | Acesso a funções de hardware (GPS, câmara…), localização, processos em background, notificações |
| **Bibliotecas nativas (C/C++)** | Expostas ao programador pela framework |
| **Android Runtime** | Bibliotecas core de Java + máquina virtual (inicialmente **Dalvik**, hoje **ART**) |
| **HAL** (Hardware Abstraction Layer) | Interfaces padrão que expõem as capacidades de hardware à API Java |
| **Kernel Linux** | Gestão de memória, processos e segurança |

### 2.6 Versões da plataforma e níveis de API
| Nome | Versão | Lançamento | API |
|---|---|---|---|
| (sem nome) | 1.0 | 23 set 2008 | 1 |
| (sem nome) | 1.1 | 9 fev 2009 | 2 |
| Cupcake | 1.5 | 27 abr 2009 | 3 |
| Donut | 1.6 | 15 set 2009 | 4 |
| Eclair | 2.0 / 2.0.1 / 2.1 | 27 out 2009 | 5; 6; 7 |
| Froyo | 2.2 – 2.2.3 | 20 mai 2010 | 8 |
| Gingerbread | 2.3 – 2.3.7 | 6 dez 2010 | 9; 10 |
| Honeycomb | 3.0 – 3.2.6 | 22 fev 2011 | 11; 12; 13 |
| Ice Cream Sandwich | 4.0 – 4.0.4 | 18 out 2011 | 14; 15 |
| Jelly Bean | 4.1 – 4.3.1 | 9 jul 2012 | 16; 17; 18 |
| KitKat | 4.4 – 4.4W.2 | 31 out 2013 | 19; 20 |
| Lollipop | 5.0 – 5.1.1 | 4 nov 2014 | 21; 22 |
| Marshmallow | 6.0 – 6.0.1 | 2 out 2015 | 23 |
| Nougat | 7.0 – 7.1.2 | 22 ago 2016 | 24; 25 |
| Oreo | 8.0 / 8.1 | 21 ago 2017 | 26; 27 |
| Pie | 9 | 6 ago 2018 | 28 |
| Android 10 | 10 | 3 set 2019 | 29 |
| Android 11 | 11 | 8 set 2020 | 30 |
| Android 12 | 12 | 4 out 2021 | 31 |
| Android 12L | 12.1 | 7 mar 2022 | 32 |
| Android 13 (Tiramisu) | 13 | 15 ago 2022 | 33 |
| Android 14 (Upside Down Cake) | 14 | 4 out 2023 | 34 |
| Android 15 (Vanilla Ice Cream) | 15 | 15 out 2024 | 35 |
| Android 16 (Baklava) | 16 | 10 jun 2025 | 36 |
| Android 17 (Cinnamon Bun) | 17 | previsto ainda em 2026 | 37 |

> **Relevância para o projeto:** as fichas pedem *Minimum API Level* **API 27 (Oreo, Android 8.1)**.

### 2.7 Novidades por versão (resumo dos slides)
- **Froyo (API 8):** velocidade, interatividade, navegação mais rápida, Android Market com atualizações automáticas.
- **Gingerbread (9–10):** API para jogos, **NFC**, melhor gestão de energia, copiar/colar com toque, várias câmaras (incl. frontal), SIP-VoIP.
- **Jelly Bean (16–18):** mais fluido; múltiplas contas com perfis restritivos; notificações com ações; Bluetooth melhor.
- **KitKat (19):** controlo por voz; menos elementos de UI; smart dialer; impressão Wi-Fi; **ART** como runtime experimental.
- **Lollipop (21–22):** múltiplos ecrãs; **Material Design**; Wi-Fi/Bluetooth na barra de notificações; **ART substitui oficialmente o Dalvik**.
- **Marshmallow (23):** **Doze** (poupança de bateria em standby); **melhor modelo de permissões**; SD adotável como memória interna; assistente de voz; impressões digitais.
- **Nougat (24–25):** múltiplos dispositivos; VR; notificações melhores; encriptação nativa; Doze melhorado.
- **Oreo (26–27):** relógio/data menores no lock screen; TTS melhorado; ícones de sistema padronizados.
- **Pie (28):** navegação por gestos; bateria/brilho adaptativos; **App Actions**; **Slices**; acesso restrito ao microfone/sensores em background; modo sem distrações / saúde digital.
- **Android 10 (29):** multi-janela, 5G, smart reply, gestos, privacidade, **dark theme**.
- **Android 11 (30):** permissões únicas; chat bubbles; dark mode automático por horário; ecrãs hole-punch/waterfall; IDs digitais.
- **Android 12 (31):** **Material You**, widgets/notificações, Quad Bayer, Wi-Fi Aware, mais privacidade.
- **Android 13 (33):** novo clipboard UI, gesto "Voltar" preditivo, ícones temáticos, idioma por app, BLE audio, MIDI 2.0, tablets.
- **Android 14 (34):** bateria, lock screen com IA, segurança, escala/fontes grandes, integração com wearables e veículos.
- **Android 15 (35):** câmara/multimédia, Wallet, proteção contra roubo, cover screen, OpenJDK 17, deteção de gravação de ecrã.
- **Android 16 (36):** notificações centradas no progresso, apps adaptativas para ecrãs grandes (modo desktop), **Material 3 Expressive**, câmara para fotógrafos profissionais.
- **Android 17 (37):** mais formatos (dobráveis, TVs), memória/desempenho, anti-phishing/malware, ligações mais privadas.

### 2.8 Bibliografia do capítulo
- <http://developer.android.com/guide/index.html> · <http://developer.android.com/about/versions> · <http://developer.android.com/about/dashboards/index.html>
- Wikipédia: *Android version history*, *List of features in Android*
- <http://developer.android.com/sdk/index.html>

---

## 3. Ficha 1 — IDE e introdução à programação em Android

**Objetivos:** criar a primeira app; conhecer o IDE e os seus componentes; correr uma app.

### 3.1 Criar o projeto "Hello World"
1. Abrir o Android Studio (em sala: *Android Admin*).
2. **New project**.
3. Escolher **Empty Views Activity**.
4. Configurar: nome da app, *Package Name*, pasta de destino.
   - Linguagem: **Java**
   - *Minimum API level*: **API 27 (Oreo; Android 8.1)**
   - ⚠ O nome e o caminho da pasta **não podem ter espaços nem caracteres especiais**.
5. Estrutura visível no IDE: ficheiros do projeto, árvore de componentes, componente selecionado e painel de propriedades.
6. Executar: **Run** ou **Shift+F10**.
   - Novo emulador: *Device Manager* (lado direito) → **+ Add a new device**.
   - Dispositivo físico: ativar **Opções de programador** (Definições → Informações do dispositivo → tocar **5 vezes** em *Número de compilação*) e ativar **Depuração USB**.
7. Mudar o nome da app de "Hello World" para **"Ficha1"**.
8. Colocar na `MainActivity` o código dos exercícios do PowerPoint *Sintaxe de Java para Android* (ver secção 4).
9. **Exercício das notas:** `[12, 14, 10, 15, 8, 6, 18, 9, 17, 11]` (escala 0–20). Calcular e apresentar:
   - 9.1 média, nota mais alta e mais baixa;
   - 9.2 quantidade e percentagem de notas positivas.
10. Refatorar para uma classe **`Estatistica`** com os métodos:
    - `calcMedia(notas)` — média
    - `calcMaior(notas)` — maior nota
    - `calcMenor(notas)` — menor nota
    - `calcPercentagemPositivos(limPos, notas)` — percentagem de positivas; `limPos` é o valor de referência (**10**)

### 3.2 Solução-modelo (exemplo de implementação)
```java
public class Estatistica {
    public static double calcMedia(int[] notas) {
        int soma = 0;
        for (int n : notas) soma += n;
        return (double) soma / notas.length;
    }
    public static int calcMaior(int[] notas) {
        int maior = notas[0];
        for (int n : notas) if (n > maior) maior = n;
        return maior;
    }
    public static int calcMenor(int[] notas) {
        int menor = notas[0];
        for (int n : notas) if (n < menor) menor = n;
        return menor;
    }
    public static double calcPercentagemPositivos(int limPos, int[] notas) {
        int positivos = 0;
        for (int n : notas) if (n >= limPos) positivos++;
        return 100.0 * positivos / notas.length;
    }
}
```
> *Esta solução é nossa (a ficha só dá o enunciado). `>= limPos` assume que 10 é positiva.*

---

## 4. Sintaxe da linguagem Java para Android (slides)

### 4.1 Generalidades
- Sintaxe baseada em **C** (delimitadores de bloco, terminadores, decisão, repetição, tipos).
- Diferença: **classes com atributos e métodos** em vez de apenas funções/variáveis.
- Blocos entre chavetas `{ }`; instruções terminam em `;`.
- Comentários: `//` linha; `/* */` bloco; `/** */` documentação; `//TODO` tarefas por concluir.

### 4.2 Tipos de dados
Java suporta dois tipos: **primitivos** (inteiros, reais, caracteres, `boolean`) e **referências** (classes e interfaces).

| Primitivo | Tamanho | Mínimo | Máximo | Classe *wrapper* |
|---|---|---|---|---|
| `boolean` | 1 bit | – | – | `Boolean` |
| `char` | 16 bit | Unicode 0 | Unicode 2¹⁶−1 | `Character` |
| `byte` | 8 bit | −128 | 127 | `Byte` |
| `short` | 16 bit | −32 768 | 32 767 | `Short` |
| `int` | 32 bit | −2 147 483 648 | 2 147 483 647 | `Integer` |
| `long` | 64 bit | −9,22×10¹⁸ | 9,22×10¹⁸ | `Long` |
| `float` | 32 bit | 3,4e−38 | 3,4e+38 | `Float` |
| `double` | 64 bit | 1,7e−308 | 1,7e+308 | `Double` |

- Todos os tipos têm **tamanho fixo**; inteiros **têm sinal** (não existe `unsigned`).
- Todos têm classe de adaptação (*wrapper*) para transformar primitivo em objeto.

### 4.3 Variáveis
- Podem ser declaradas em qualquer ponto.
- Dentro de um bloco não se pode redeclarar o nome de uma variável do bloco exterior.
- Convenção: começam por **minúscula**.
- Têm de ser **inicializadas antes de usar**.

### 4.4 Operadores
| Grupo | Operadores | Notas |
|---|---|---|
| Atribuição | `=  +=  -=  *=  /=  %=` | não se aplicam a booleanos |
| Aritméticos | `+ - * / %  ++ --` | não se aplicam a booleanos |
| Relacionais | `> >= < <= == !=` | resultado é `boolean`; `>`, `<`… **não** se aplicam a `boolean` (só `==` e `!=`) |
| Lógicos | `&& \|\| !` | só sobre `boolean`; resultado `boolean` |
| Condicional (ternário) | `cond ? a : b` | a condição tem de ser `boolean` |
| Cast | `(tipo)` | conversão de menor→maior é automática; maior→menor tem de ser **explícita** |

### 4.5 Estruturas de controlo
Iguais às de C, **mas todas as condições têm de ser `boolean`**:
```java
int a = 3;
if (a != 0) { }      // em C seria if (a)
boolean b = true;
if (b) { }
```
Decisão: `if/else`, `switch`. Repetição: `for`, `for-each`, `while`, `do..while`, `break`, `continue`.

### 4.6 Exercícios dos slides (com código)
```java
// Ex 1 — if-else e ternário
boolean eMaiorIf(int valor1, int valor2) {
    if (valor1 > valor2) return true; else return false;
}
boolean eMaiorTernario(int valor1, int valor2) {
    return valor1 > valor2 ? true : false;
}

// Ex 2 — switch (vogal / consoante / letra estrangeira)
void eSwitch(char opcao) {
    switch (Character.toUpperCase(opcao)) {
        case 'A': case 'E': case 'I': case 'O': case 'U':
            System.out.println("R: A opção selecionada foi uma vogal"); break;
        case 'Y': case 'W': case 'K':
            System.out.println("R: A opção selecionada foi uma letra do alfabeto estrangeiro"); break;
        default:
            System.out.println("R: A opção selecionada foi uma consoante");
    }
}
// (no slide há um erro de digitação: case '0' em vez de 'O'.)

// Ex 3 — for (inteiros entre dois números, ordem crescente)
void eFor(int n1, int n2) {
    if (n1 <= n2) for (int i = n1; i <= n2; i++) System.out.println("I = " + i);
    else          for (int i = n2; i <= n1; i++) System.out.println("I = " + i);
}

// Ex 4 — while
void eWhile(int n1, int n2) {
    int j, max;
    if (n1 <= n2) { j = n1; max = n2; } else { j = n2; max = n1; }
    while (j <= max) { System.out.println("J = " + j); j++; }
}

// Ex 5 — do..while
void eDoWhile(int n1, int n2) {
    int k, max;
    if (n1 <= n2) { k = n1; max = n2; } else { k = n2; max = n1; }
    do { System.out.println("K = " + k); k++; } while (k <= max);
}

// Ex 6 — foreach
void eForeach() {
    int[] numeros = { 8, 2, 13, 4, 1, 2, 7 };
    for (int n : numeros) System.out.println("Num = " + n);
}
```

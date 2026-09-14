# Avec Clone

Clona os dados e a UI funcional do painel [Avec Pro](https://terminal.avec.beauty) (Agenda,
Comandas, Comissões, Clientes) em um site WordPress, para permitir montar um BI em cima desses
dados — algo que o Avec não oferece nativamente.

O projeto tem duas partes:

1. **`tools/`** (Node.js/TypeScript) — extrai os dados reais do Avec, e gera os arquivos WXR
   (formato de import/export do WordPress) usados pelo plugin.
2. **`plugin/avec-clone/`** (PHP + WordPress) — registra os Custom Post Types, importa o WXR, e
   expõe os dados numa área logada (`/minha-conta/`) mobile-first, além do admin nativo do WP.

Veja `docs/api-reconnaissance.md` para o mapeamento da API do Avec, e o histórico da conversa que
gerou este projeto para o contexto completo das decisões de escopo/privacidade.

## Pré-requisitos

- Node.js 20+ e npm
- PHP 7.4+ e [Composer](https://getcomposer.org)
- [Docker](https://www.docker.com) rodando (necessário para os testes de integração do plugin via `wp-env`)
- `.env` na raiz do repo com as credenciais do Avec (copie de `.env.example`):
  ```
  AVEC_LOGIN_URL=https://terminal.avec.beauty/login/<slug-do-salao>
  AVEC_EMAIL=...
  AVEC_PASSWORD=...
  ```

## ⚠️ Privacidade e segurança

- **`data/real/`** (saída do extractor) é **inteiramente ignorado pelo git** — nunca commite nem
  dê push nesse conteúdo. Só `data/fake/` (dataset sintético) é versionado.
- O repositório GitHub (`joselisou/avec`) deve ficar **privado** sempre que houver qualquer chance
  de dados reais estarem no histórico. Alternar visibilidade: `gh repo edit joselisou/avec --visibility public|private`.
- O extractor faz login de verdade no Avec. **O Avec permite só uma sessão ativa por conta** — não
  rode o extractor enquanto estiver navegando manualmente logado com o mesmo usuário (a sessão do
  navegador cai).
- O extractor é deliberadamente lento (concorrência 1, pausa de 400ms entre chamadas) para não
  sobrecarregar a API de produção do Avec nem correr risco de bloqueio por rate-limit. Não aumente
  esses valores sem necessidade real.

## Rodando a extração de dados reais

```bash
cd tools/extractor
npm install
npx playwright install chromium   # só na primeira vez
npx tsx src/index.ts
```

Por padrão, extrai de `2025-07-19` até hoje. Opções:

| Flag | Efeito |
|---|---|
| `--from=YYYY-MM-DD --to=YYYY-MM-DD` | Limita o período (útil para testes rápidos) |
| `--force` | Ignora o cache local em `data/real/raw/` e refaz tudo |

**Tempo esperado**: para o período completo (~14 meses), espere algo entre **15 e 30 minutos** —
o script é sequencial e pausado de propósito (veja a seção de privacidade acima). Ele é resumível:
se for interrompido, rodar de novo sem `--force` pula o que já foi extraído.

Saída:
- `data/real/raw/<entidade>/*.json` — resposta bruta de cada chamada (para depuração)
- `data/real/normalized/{agenda,comandas,clientes}.json` — consolidado
- `data/real/extraction-summary.json` — contagens (dias, agendamentos, comandas, clientes)

## Gerando o WXR

### Dataset fake (versionado, usado no Playground)

```bash
cd tools/fake-data-generator
npm install
npx tsx src/index.ts
```

Gera `data/fake/json/dataset.json` e `data/fake/avec-fake-dataset.xml` (determinístico, seed fixa
— só muda se o gerador mudar).

### Dataset real (local, nunca commitado)

Ainda não há um script único "JSON real → WXR real" (o `wxr-builder` hoje só é chamado pelo
gerador fake). Para importar dados reais, use a tela de admin do plugin (**Avec Clone > Importar
WXR**) ou `wp avec-clone import-wxr <arquivo.xml>` depois de montar o XML a partir dos mappers em
`tools/wxr-builder/src/`.

## Plugin WordPress (`plugin/avec-clone`)

```bash
cd plugin/avec-clone
composer install       # PHPCS/WPCS + PHPUnit
npm install             # wp-env + wp-scripts (build do front-end)
npm run build            # compila assets/src/{scss,ts} → assets/build/
```

### Rodar/testar localmente (wp-env)

```bash
npm run env:start        # sobe WordPress em http://localhost:8888 (admin/password) e :8889 (testes)
npm run test:php          # roda a suíte PHPUnit de integração contra o :8889
npm run env:destroy       # derruba os containers quando terminar
```

### Qualidade de código

```bash
composer lint             # PHPCS/WPCS
composer lint:fix          # phpcbf (autofix)
npm run lint:js            # ESLint (assets/src/ts)
npm run lint:css           # stylelint (assets/src/scss)
```

### Import/limpeza via WP-CLI

```bash
wp avec-clone import-wxr caminho/para/dataset.xml
wp avec-clone clean       # remove só os posts importados (identificados por _avec_source_id)
```

## Abrir no WordPress Playground

Com o repositório **público**, o link abaixo instala o plugin (direto do GitHub, sem precisar de
build/zip) e importa o dataset fake automaticamente:

```
https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/joselisou/avec/main/blueprint.json
```

Enquanto o repo estiver privado esse link não funciona (raw.githubusercontent.com não serve
conteúdo de repos privados) — teste localmente primeiro com a CLI do Playground:

```bash
npx @wp-playground/cli run-blueprint --blueprint=./blueprint.json
```

(nesse teste local, os passos que referenciam URLs do GitHub também vão falhar até o push real —
sirva como checagem de sintaxe do blueprint, não de conteúdo).

## CI

`.github/workflows/ci.yml` roda em todo push/PR: lint + testes dos três pacotes Node, PHPCS/WPCS
do plugin, lint+build dos assets do front-end (falha se `assets/build/` estiver desatualizado),
PHPUnit via wp-env, e uma checagem de que os WXR gerados (fixture de teste e dataset fake) batem
com o que os geradores produzem.

## Estrutura

```
avec/
├── docs/api-reconnaissance.md   # schema dos endpoints reais do Avec (sem dados sensíveis)
├── tools/
│   ├── extractor/               # login + extração via API real do Avec
│   ├── wxr-builder/              # JSON → WXR (WordPress eXtended RSS)
│   └── fake-data-generator/      # dataset sintético para demo pública
├── data/
│   ├── real/                     # GITIGNORED — saída real da extração
│   └── fake/                     # versionado — dataset sintético
├── plugin/avec-clone/            # o plugin WordPress
└── blueprint.json                # link "Open in Playground"
```

import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { buildWxr, type WxrPost } from '../src/wxrBuilder.js';

const here = path.dirname(fileURLToPath(import.meta.url));
const outPath = path.resolve(
  here,
  '../../../plugin/avec-clone/tests/fixtures/sample.xml',
);

const posts: WxrPost[] = [
  {
    postType: 'avec_cliente',
    title: 'Cliente Fixture Um',
    postDate: '2025-07-19 09:00:00',
    meta: { _avec_source_id: 1, _avec_email: 'fixture.um@example.com', _avec_celular: '11999990001' },
  },
  {
    postType: 'avec_cliente',
    title: 'Cliente Fixture Dois',
    postDate: '2025-07-19 09:10:00',
    meta: { _avec_source_id: 2, _avec_email: 'fixture.dois@example.com', _avec_celular: '11999990002' },
  },
  {
    postType: 'avec_comanda',
    title: 'Comanda #1',
    postDate: '2025-07-19 10:00:00',
    meta: {
      _avec_source_id: 100,
      _avec_numero: 1,
      _avec_data: '2025-07-19 00:00:00',
      _avec_total: 90,
      _avec_cliente_source_id: 1,
    },
  },
  {
    postType: 'avec_recibo',
    title: 'Corte Feminino',
    postDate: '2025-07-19 10:00:00',
    meta: {
      _avec_source_id: 1000,
      _avec_valor: 90,
      _avec_comissao: 40,
      _avec_comanda_source_id: 100,
    },
    terms: [{ taxonomy: 'avec_recibo_tipo', name: 'salao_servicos' }],
  },
  {
    postType: 'avec_agendamento',
    title: 'Corte Feminino — Cliente Fixture Um',
    postDate: '2025-07-19 00:00:00',
    meta: {
      _avec_source_id: 10000,
      _avec_data: '2025-07-19',
      _avec_hora_inicio: 570, // 09:30
      _avec_hora_fim: 630, // 10:30
      _avec_valor: 90,
      _avec_servico: 'Corte Feminino',
      _avec_cliente_source_id: 1,
      _avec_comanda_source_id: 100,
    },
    terms: [{ taxonomy: 'avec_agendamento_status', name: 'concluido' }],
  },
];

const xml = buildWxr(posts, {
  siteTitle: 'Avec Clone (test fixture)',
  siteUrl: 'http://localhost:8888',
  authorLogin: 'admin',
});

await fs.mkdir(path.dirname(outPath), { recursive: true });
await fs.writeFile(outPath, xml);
console.log(`Wrote fixture with ${posts.length} posts to ${outPath}`);

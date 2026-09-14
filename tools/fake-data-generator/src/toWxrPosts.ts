import type { FakeDataset } from './fakeDataset.js';

// Mirrors the WxrPost shape from @avec-clone/wxr-builder without adding a package dependency
// (both tools live in this monorepo-lite layout and import each other's TS sources directly).
export interface WxrPostLike {
  postType: string;
  title: string;
  postDate: string;
  meta?: Record<string, string | number | null>;
  terms?: Array<{ taxonomy: string; name: string }>;
}

/** Maps a fake (or real, once shapes are confirmed identical) dataset into flat WXR posts. */
export function toWxrPosts(dataset: FakeDataset): WxrPostLike[] {
  const posts: WxrPostLike[] = [];

  for (const cliente of dataset.clientes) {
    posts.push({
      postType: 'avec_cliente',
      title: cliente.nome,
      postDate: cliente.datacad,
      meta: {
        _avec_source_id: cliente.id,
        _avec_email: cliente.email,
        _avec_telefone: cliente.telefone,
        _avec_celular: cliente.celular,
      },
    });
  }

  for (const comanda of dataset.comandas) {
    const total = comanda.tab_items.reduce((sum, item) => sum + item.valor - item.desconto, 0);

    posts.push({
      postType: 'avec_comanda',
      title: `Comanda #${comanda.numero}`,
      postDate: comanda.data,
      meta: {
        _avec_source_id: comanda.id,
        _avec_numero: comanda.numero,
        _avec_data: comanda.data,
        _avec_total: total,
        _avec_cliente_source_id: comanda.salao_cliente_id,
      },
    });

    for (const item of comanda.tab_items) {
      posts.push({
        postType: 'avec_recibo',
        title: item.item,
        postDate: item.datacad,
        meta: {
          _avec_source_id: item.id,
          _avec_valor: item.valor,
          _avec_desconto: item.desconto,
          _avec_comissao: item.comissao,
          _avec_profissional_id: item.profissional_id,
          _avec_status: item.status,
          _avec_comanda_source_id: item.comanda_id,
        },
        terms: [{ taxonomy: 'avec_recibo_tipo', name: item.tipo }],
      });
    }
  }

  for (const booking of dataset.bookings) {
    posts.push({
      postType: 'avec_agendamento',
      title: `${booking.servicos} — ${booking.cliente_nome}`,
      postDate: `${booking.data} 00:00:00`,
      meta: {
        _avec_source_id: booking.id,
        _avec_data: booking.data,
        _avec_hora_inicio: booking.hora_ini,
        _avec_hora_fim: booking.hora_fim,
        _avec_valor: booking.valor,
        _avec_servico: booking.servicos,
        _avec_cliente_source_id: booking.salao_cliente_id,
        _avec_comanda_source_id: booking.comanda_id,
      },
      terms: [{ taxonomy: 'avec_agendamento_status', name: booking.status_agendamento }],
    });
  }

  return posts;
}

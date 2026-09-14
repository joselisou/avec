import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import pLimit from 'p-limit';
import { config } from './config.js';
import { AvecApiClient } from './client.js';
import { eachDate, todayIso } from './dateRange.js';
import { fetchAgendaDay, type Booking } from './endpoints/agenda.js';
import { fetchComandaDetail, type ComandaDetail } from './endpoints/comanda.js';
import { fetchClienteDetail, type SalonClient } from './endpoints/cliente.js';
import { collectClienteIds } from './collectClienteIds.js';

const here = path.dirname(fileURLToPath(import.meta.url));
const dataRoot = path.resolve(here, '../../../data/real');

// Sequential on purpose — this hits Avec's real production API, not a sandbox. Combined with the
// fixed per-request delay in AvecApiClient.get(), this keeps load light and spread out instead
// of bursty, to avoid tripping rate limits or adding noticeable load to their system.
const AGENDA_CONCURRENCY = 1;
const COMANDA_CONCURRENCY = 1;
const CLIENTE_CONCURRENCY = 1;

async function readJsonIfExists<T>(filePath: string): Promise<T | null> {
  try {
    return JSON.parse(await fs.readFile(filePath, 'utf8')) as T;
  } catch (error) {
    if ((error as NodeJS.ErrnoException).code === 'ENOENT') return null;
    throw error;
  }
}

async function writeJson(filePath: string, data: unknown): Promise<void> {
  await fs.mkdir(path.dirname(filePath), { recursive: true });
  await fs.writeFile(filePath, JSON.stringify(data, null, 2));
}

interface CliOptions {
  from: string;
  to: string;
  force: boolean;
}

function parseArgs(argv: string[]): CliOptions {
  const args = new Map<string, string>();
  for (const arg of argv) {
    const [key, value] = arg.replace(/^--/, '').split('=');
    args.set(key, value ?? 'true');
  }
  return {
    from: args.get('from') ?? config.extractionStartDate,
    to: args.get('to') ?? todayIso(),
    force: args.get('force') === 'true',
  };
}

async function main(): Promise<void> {
  const { from, to, force } = parseArgs(process.argv.slice(2));
  console.log(`Extracting Avec Pro data from ${from} to ${to} (force=${force})`);

  const client = await AvecApiClient.create();
  console.log(`Logged in. salon_id=${client.salonId} professional_id=${client.professionalId}`);

  const agendaLimit = pLimit(AGENDA_CONCURRENCY);
  const dates = eachDate(from, to);
  const allBookings: Booking[] = [];
  const comandaIds = new Set<number>();

  await Promise.all(
    dates.map((isoDate) =>
      agendaLimit(async () => {
        const rawPath = path.join(dataRoot, 'raw', 'agenda', `${isoDate}.json`);
        let day = await (force ? Promise.resolve(null) : readJsonIfExists<{ bookings: Booking[] }>(rawPath));

        if (!day) {
          day = await fetchAgendaDay(client, isoDate);
          await writeJson(rawPath, day);
        }

        for (const booking of day.bookings) {
          allBookings.push(booking);
          if (booking.comanda_id) comandaIds.add(booking.comanda_id);
        }
      }),
    ),
  );

  console.log(`Agenda: ${allBookings.length} bookings across ${dates.length} days, ${comandaIds.size} unique comandas.`);

  const comandaLimit = pLimit(COMANDA_CONCURRENCY);
  const comandas: ComandaDetail[] = [];

  await Promise.all(
    Array.from(comandaIds).map((comandaId) =>
      comandaLimit(async () => {
        const rawPath = path.join(dataRoot, 'raw', 'comandas', `${comandaId}.json`);
        let detail = await (force ? Promise.resolve(null) : readJsonIfExists<ComandaDetail>(rawPath));

        if (!detail) {
          detail = await fetchComandaDetail(client, comandaId);
          await writeJson(rawPath, detail);
        }

        comandas.push(detail);
      }),
    ),
  );

  console.log(`Comandas: ${comandas.length} fetched.`);

  const clienteIds = collectClienteIds(allBookings, comandas);
  const clienteLimit = pLimit(CLIENTE_CONCURRENCY);
  const clientes: SalonClient[] = [];

  await Promise.all(
    clienteIds.map((clienteId) =>
      clienteLimit(async () => {
        const rawPath = path.join(dataRoot, 'raw', 'clientes', `${clienteId}.json`);
        let detail = await (force ? Promise.resolve(null) : readJsonIfExists<SalonClient>(rawPath));

        if (!detail) {
          detail = await fetchClienteDetail(client, clienteId);
          await writeJson(rawPath, detail);
        }

        clientes.push(detail);
      }),
    ),
  );

  console.log(`Clientes: ${clientes.length} fetched.`);

  await writeJson(path.join(dataRoot, 'normalized', 'agenda.json'), allBookings);
  await writeJson(path.join(dataRoot, 'normalized', 'comandas.json'), comandas);
  await writeJson(path.join(dataRoot, 'normalized', 'clientes.json'), clientes);

  await writeJson(path.join(dataRoot, 'extraction-summary.json'), {
    extractedAt: new Date().toISOString(),
    range: { from, to },
    counts: {
      days: dates.length,
      bookings: allBookings.length,
      comandas: comandas.length,
      clientes: clientes.length,
    },
  });

  console.log('Done. See data/real/extraction-summary.json for counts.');
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});

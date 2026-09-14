import { config as loadEnv } from 'dotenv';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const here = path.dirname(fileURLToPath(import.meta.url));
// Repo root is two levels up from tools/extractor/src.
loadEnv({ path: path.resolve(here, '../../../.env') });

function requireEnv(name: string): string {
  const value = process.env[name];
  if (!value) {
    throw new Error(`Missing required env var: ${name}`);
  }
  return value;
}

export const config = {
  loginUrl: requireEnv('AVEC_LOGIN_URL'),
  email: requireEnv('AVEC_EMAIL'),
  password: requireEnv('AVEC_PASSWORD'),
  apiBaseUrl: 'https://api.avec.beauty',
  extractionStartDate: '2025-07-19',
  /** Fixed pause after every successful API call, to spread load gently on Avec's production API. */
  requestDelayMs: 400,
};

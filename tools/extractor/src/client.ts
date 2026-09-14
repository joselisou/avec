import { config } from './config.js';
import { login, type Session } from './auth.js';

const MAX_RETRIES = 3;

export class AvecApiClient {
  private session: Session;

  private constructor(session: Session) {
    this.session = session;
  }

  static async create(): Promise<AvecApiClient> {
    const session = await login();
    return new AvecApiClient(session);
  }

  get salonId(): string {
    return this.session.salonId;
  }

  get professionalId(): string {
    return this.session.professionalId;
  }

  /** GET `path` under the Avec API, decoded as JSON. Retries on 5xx/429 and re-logs in once on 401. */
  async get<T>(path: string, searchParams?: Record<string, string>): Promise<T> {
    const url = new URL(path, config.apiBaseUrl);
    for (const [key, value] of Object.entries(searchParams ?? {})) {
      url.searchParams.set(key, value);
    }

    let attempt = 0;
    let reloggedIn = false;

    for (;;) {
      attempt += 1;
      const response = await fetch(url, {
        headers: {
          authorization: this.session.token,
          accept: 'application/json',
        },
      });

      if (response.status === 401 && !reloggedIn) {
        reloggedIn = true;
        this.session = await login();
        continue;
      }

      if ((response.status === 429 || response.status >= 500) && attempt <= MAX_RETRIES) {
        const backoffMs = 500 * 2 ** (attempt - 1);
        await new Promise((resolve) => setTimeout(resolve, backoffMs));
        continue;
      }

      if (!response.ok) {
        throw new Error(`GET ${url} failed: ${response.status} ${await response.text()}`);
      }

      const body = (await response.json()) as { code: number; data: T };
      return body.data;
    }
  }
}

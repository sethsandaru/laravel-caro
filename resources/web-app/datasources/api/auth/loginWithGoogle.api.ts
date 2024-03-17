import { assertOutcomeSuccess, httpClient } from '@/datasources/api/axios';

export const loginWithGoogleApi = (code: string) =>
  httpClient
    .post('/auth/google', { code })
    .then(assertOutcomeSuccess)
    .catch(() => false);

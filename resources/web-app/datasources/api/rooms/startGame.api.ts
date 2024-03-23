import { assertOutcomeSuccess, httpClient } from '@/datasources/api/axios';

export const startGameApi = (roomId: string) =>
  httpClient
    .post(`/rooms/${roomId}/start-new-game`)
    .then(assertOutcomeSuccess)
    .catch(() => false);

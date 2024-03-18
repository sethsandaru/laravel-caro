import { assertOutcomeSuccess, httpClient } from '@/datasources/api/axios';

export const markAsUnReadyForRoomByIdApi = (id: string) =>
  httpClient
    .patch(`/rooms/${id}/unready`)
    .then(assertOutcomeSuccess)
    .catch(() => false);

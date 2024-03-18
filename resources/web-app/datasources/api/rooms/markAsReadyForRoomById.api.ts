import { assertOutcomeSuccess, httpClient } from '@/datasources/api/axios';

export const markAsReadyForRoomByIdApi = (id: string) =>
  httpClient
    .patch(`/rooms/${id}/ready`)
    .then(assertOutcomeSuccess)
    .catch(() => false);

import { assertOutcomeSuccess, httpClient } from '@/datasources/api/axios';

export const setMoveInBoardApi = ({
  roomId,
  roomGameId,
  ...data
}: {
  roomId: string;
  roomGameId: string;
  rowIndex: number;
  colIndex: number;
}) =>
  httpClient
    .post(`/rooms/${roomId}/games/${roomGameId}/move`, data)
    .then(assertOutcomeSuccess)
    .catch(() => false);

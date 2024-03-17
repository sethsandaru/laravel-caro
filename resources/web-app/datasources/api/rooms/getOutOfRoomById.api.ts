import { httpClient } from '@/datasources/api/axios';
import { AxiosError, AxiosResponse } from 'axios';

export const getOutOfRoomByIdApi = (id: string) =>
  httpClient
    .patch(`/rooms/${id}/get-out`)
    .then((res: AxiosResponse<{ outcome: 'SUCCESS' }>) => res.data.outcome)
    .catch((err: AxiosError) => {
      return 'UNKNOWN';
    });

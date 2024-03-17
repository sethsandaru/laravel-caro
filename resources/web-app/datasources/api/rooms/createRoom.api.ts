import { httpClient } from '@/datasources/api/axios';
import { AxiosResponse } from 'axios';

export const createRoomApi = (title: string) =>
  httpClient
    .post('/rooms', {
      title,
    })
    .then(
      (res: AxiosResponse<{ outcome: 'SUCCESS'; roomId: string }>) =>
        res.data.roomId
    );

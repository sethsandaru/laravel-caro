import { httpClient } from '@/datasources/api/axios';
import { AxiosResponse } from 'axios';
import { Room } from '@/datasources/api/rooms/getRooms.api';

export type DetailedRoom = Room & {
  createdByUser: { name: string };
  secondUser: { name: string } | null;
};

export const getRoomByIdApi = (id: string) =>
  httpClient
    .get(`/rooms/${id}`)
    .then(
      (res: AxiosResponse<{ outcome: 'SUCCESS'; room: DetailedRoom }>) =>
        res.data
    );

import { httpClient } from '@/datasources/api/axios';
import { AxiosResponse } from 'axios';
import { Room } from '@/datasources/api/rooms/getRooms.api';
import { LoggedInUser } from '@/datasources/api/auth/getLoggedInUser.api';

export type DetailedRoom = Room & {
  createdByUser: LoggedInUser;
  secondUser: LoggedInUser | null;
};

export const getRoomByIdApi = (id: string) =>
  httpClient
    .get(`/rooms/${id}`)
    .then(
      (res: AxiosResponse<{ outcome: 'SUCCESS'; room: DetailedRoom }>) =>
        res.data
    );

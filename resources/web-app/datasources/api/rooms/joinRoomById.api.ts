import { httpClient } from '@/datasources/api/axios';
import { AxiosError, AxiosResponse } from 'axios';
import { Room } from '@/datasources/api/rooms/getRooms.api';
import { LoggedInUser } from '@/datasources/api/auth/getLoggedInUser.api';

export const joinRoomByIdApi = (id: string) =>
  httpClient
    .patch(`/rooms/${id}/join`)
    .then((res: AxiosResponse<{ outcome: 'SUCCESS' }>) => res.data.outcome)
    .catch((err: AxiosError) => {
      if (err.response?.status === 403) {
        return 'ALREADY_HAVE_MEMBER_JOINED';
      }

      return 'UNKNOWN';
    });

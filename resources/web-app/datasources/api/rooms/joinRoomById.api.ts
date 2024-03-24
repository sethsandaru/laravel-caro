import { httpClient } from '@/datasources/api/axios';
import { AxiosError, AxiosResponse } from 'axios';
import { Room } from '@/datasources/api/rooms/getRooms.api';
import { LoggedInUser } from '@/datasources/api/auth/getLoggedInUser.api';
import { showErrorAlert } from '@/utils/toast';

export const joinRoomByIdApi = (id: string) =>
  httpClient
    .patch(`/rooms/${id}/join`)
    .then((res: AxiosResponse<{ outcome: 'SUCCESS' }>) => res.data.outcome)
    .catch((err: AxiosError<Record<string, unknown>>) => {
      if (err.response?.status === 403) {
        return 'ALREADY_HAVE_MEMBER_JOINED';
      }

      if (err.response?.status === 404) {
        return 'ROOM_NOT_FOUND';
      }

      if (err.response?.data?.outcome === 'ALREADY_IN_A_ROOM') {
        const room = err.response?.data.room as { name: string };

        showErrorAlert(
          `Bạn đang ở trong phòng: "${room.name}". Hãy thoát khỏi phòng trước khi tạo phòng mới`,
          'Đã có phòng riêng'
        );

        return 'ALREADY_IN_A_ROOM';
      }

      return 'UNKNOWN';
    });

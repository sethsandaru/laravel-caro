import { httpClient } from '@/datasources/api/axios';
import { AxiosResponse } from 'axios';
import { LoggedInUser } from '@/datasources/api/auth/getLoggedInUser.api';

export type RoomStatus =
  | 'WAITING_FOR_ANOTHER_PLAYER'
  | 'WAITING_FOR_CONFIRMATION'
  | 'READY_TO_PLAY'
  | 'PLAYING';

export type Room = {
  ulid: string;
  title: string;
  status: RoomStatus;
  totalPlayed: number;
  createdByUser: LoggedInUser;
  secondUser?: LoggedInUser | null;
};

export const getRoomsApi = () =>
  httpClient
    .get('/rooms')
    .then(
      (res: AxiosResponse<{ outcome: 'SUCCESS'; rooms: Room[] }>) => res.data
    );

export const getReadableRoomStatus = (status: RoomStatus) => {
  switch (status) {
    case 'PLAYING':
      return 'Đang chơi';
    case 'WAITING_FOR_ANOTHER_PLAYER':
      return 'Đợi người chơi thứ 2';
    case 'WAITING_FOR_CONFIRMATION':
      return 'Đợi sẵn sàng';
    case 'READY_TO_PLAY':
      return 'Có thể bắt đầu';
  }
};

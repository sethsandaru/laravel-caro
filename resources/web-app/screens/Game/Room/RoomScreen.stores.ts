import { defineStore } from 'pinia';
import { ref } from 'vue';
import {
  DetailedRoom,
  getRoomByIdApi,
} from '@/datasources/api/rooms/getRoomById.api';
import { LoggedInUser } from '@/datasources/api/auth/getLoggedInUser.api';
import { AxiosError } from 'axios';

export const currentRoomStore = defineStore('currentRoom', () => {
  const room = ref<DetailedRoom>();

  const setRoom = (wantedRoom: DetailedRoom) => {
    room.value = { ...wantedRoom };
  };

  const setCreatorUser = (user: LoggedInUser) => {
    if (!room.value) {
      return;
    }

    room.value.createdByUser = user;
  };

  const setSecondUser = (user: LoggedInUser | null) => {
    if (!room.value) {
      return;
    }

    room.value.secondUser = user;
  };

  const refreshRoom = async () => {
    if (!room.value) {
      return;
    }

    const roomRes = await getRoomByIdApi(room.value.ulid).catch(
      (res: AxiosError) => {
        if (res.response?.status === 403) {
          return 'UNAUTHORIZED';
        }

        return 'UNKNOWN';
      }
    );
    if (roomRes === 'UNAUTHORIZED' || roomRes === 'UNKNOWN') {
      return false;
    }

    setRoom(roomRes.room);

    return true;
  };

  return { room, setRoom, setCreatorUser, setSecondUser, refreshRoom };
});

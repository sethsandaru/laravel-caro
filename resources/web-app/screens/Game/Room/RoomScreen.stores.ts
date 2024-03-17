import { defineStore } from 'pinia';
import { ref } from 'vue';
import { DetailedRoom } from '@/datasources/api/rooms/getRoomById.api';
import { LoggedInUser } from '@/datasources/api/auth/getLoggedInUser.api';

export const currentRoomStore = defineStore('currentRoom', () => {
  const room = ref<DetailedRoom>();

  const setRoom = (wantedRoom: DetailedRoom) => {
    room.value = { ...wantedRoom };
  };

  const setSecondUser = (user: LoggedInUser) => {
    if (!room.value) {
      return;
    }

    room.value.secondUser = user;
  };

  return { room, setRoom, setSecondUser };
});

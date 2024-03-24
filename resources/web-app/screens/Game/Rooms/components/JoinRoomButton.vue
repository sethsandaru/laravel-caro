<template>
  <button
    type="button"
    class="block rounded-md bg-pink-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-pink-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-600"
    @click="joinRoom(room)"
  >
    Gia nhập
  </button>
</template>

<script setup lang="ts">
import { Room } from '@/datasources/api/rooms/getRooms.api';
import { toRef } from 'vue';
import { joinRoomByIdApi } from '@/datasources/api/rooms/joinRoomById.api';
import {
  showInfoAlert,
  showUnexpectedError,
  showWarningAlert,
} from '@/utils/toast';
import { useRouter } from 'vue-router';

type Props = {
  room: Room;
};

type Emits = {
  (e: 'refreshRooms'): void;
};

const props = defineProps<Props>();
const emits = defineEmits<Emits>();

const room = toRef(props, 'room');

const router = useRouter();

const joinRoom = async (room: Room) => {
  const res = await joinRoomByIdApi(room.ulid);

  if (res === 'ALREADY_HAVE_MEMBER_JOINED') {
    showWarningAlert(
      'Đã có người chơi khác tham gia phòng này, xin mời bạn hãy vào phòng khác.',
      'Phòng đã đủ người'
    );

    return emits('refreshRooms');
  }

  if (res === 'UNKNOWN') {
    showUnexpectedError();

    return;
  }

  showInfoAlert(
    'Vào phòng thành công, đang chuyển hướng bạn vào phòng,...',
    'Thành công'
  );
  router.push({
    name: 'room',
    params: {
      id: room.ulid,
    },
  });
};
</script>

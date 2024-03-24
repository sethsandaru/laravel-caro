<template>
  <button
    @click="createRoom"
    type="button"
    class="block rounded-md bg-pink-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-pink-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-600"
  >
    Tạo phòng mới
  </button>
</template>

<script setup lang="ts">
import {
  showInfoAlert,
  showUnexpectedError,
  showWarningAlert,
} from '@/utils/toast';
import { createRoomApi } from '@/datasources/api/rooms/createRoom.api';
import { useRouter } from 'vue-router';

const router = useRouter();

const createRoom = async () => {
  const roomName = prompt('Hãy điền tên phòng');
  if (!roomName) {
    return showWarningAlert('Tên phòng là bắt buộc', 'Lỗi tạo phòng');
  }

  const createRes = await createRoomApi(roomName).catch(() => undefined);
  if (!createRes) {
    return showUnexpectedError();
  }

  showInfoAlert(
    'Tạo phòng thành công, chuyển hướng bạn đến phòng...',
    'Thành công'
  );

  router.push({
    name: 'room',
    params: {
      id: createRes,
    },
  });
};
</script>

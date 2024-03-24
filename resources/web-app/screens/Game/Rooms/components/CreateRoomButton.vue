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
  showErrorAlert,
  showInfoAlert,
  showUnexpectedError,
  showWarningAlert,
} from '@/utils/toast';
import { createRoomApi } from '@/datasources/api/rooms/createRoom.api';
import { useRouter } from 'vue-router';
import { AxiosError } from 'axios';

const router = useRouter();

const createRoom = async () => {
  const roomName = prompt('Hãy điền tên phòng');
  if (!roomName) {
    return showWarningAlert('Tên phòng là bắt buộc', 'Lỗi tạo phòng');
  }

  const createRes = await createRoomApi(roomName).catch(
    (err: AxiosError<Record<string, unknown>>) => {
      if (err.response?.data?.outcome === 'ALREADY_IN_A_ROOM') {
        const room = err.response?.data.room as { name: string };

        showErrorAlert(
          `Bạn đang ở trong phòng: "${room.name}". Hãy thoát khỏi phòng trước khi tạo phòng mới`,
          'Đã có phòng riêng'
        );
      }

      return undefined;
    }
  );
  if (!createRes) {
    return;
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

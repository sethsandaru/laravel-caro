<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold leading-6 text-gray-900 mb-10">
          Phòng: {{ room?.title ?? 'Đang tải dữ liệu' }}
        </h1>

        <RoomHeader />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router';
import { onMounted } from 'vue';
import { getRoomByIdApi } from '@/datasources/api/rooms/getRoomById.api';
import { AxiosError } from 'axios';
import { showErrorAlert, showUnexpectedError } from '@/utils/toast';
import { currentRoomStore } from '@/screens/Game/Room/RoomScreen.stores';
import { storeToRefs } from 'pinia';
import RoomHeader from '@/screens/Game/Room/components/RoomHeader.vue';

const route = useRoute();
const router = useRouter();
const currentRoom = currentRoomStore();
const { room } = storeToRefs(currentRoom);

onMounted(async () => {
  const roomId = String(route.params.id);
  if (!roomId) {
    return router.replace({ name: 'rooms' });
  }

  const roomRes = await getRoomByIdApi(roomId).catch((res: AxiosError) => {
    if (res.response?.status === 403) {
      return 'UNAUTHORIZED';
    }

    return 'UNKNOWN';
  });

  if (roomRes === 'UNAUTHORIZED') {
    showErrorAlert('Bạn không có quyền truy cập phòng này', 'Lỗi');
    return router.replace({ name: 'rooms' });
  }

  if (roomRes === 'UNKNOWN') {
    showUnexpectedError();
    return router.replace({ name: 'rooms' });
  }

  currentRoom.setRoom(roomRes.room);
});
</script>

<template>
  <button
    type="button"
    class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
    :disabled="playing"
    :class="{
      'disabled:bg-gray-300 disabled:hover:bg-gray-300': playing,
      'bg-white hover:bg-gray-50': !playing,
    }"
    @click="leaveRoom"
  >
    <XCircleIcon
      class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400"
      aria-hidden="true"
    />
    <span>Rời phòng</span>
  </button>
</template>

<script setup lang="ts">
import { XCircleIcon } from '@heroicons/vue/24/solid';
import { getOutOfRoomByIdApi } from '@/datasources/api/rooms/getOutOfRoomById.api';
import { showInfoAlert, showUnexpectedError } from '@/utils/toast';
import { currentRoomStore } from '@/screens/Game/Room/RoomScreen.stores';
import { storeToRefs } from 'pinia';
import { useRouter } from 'vue-router';

type Props = {
  playing: boolean;
};

type Emits = {
  (e: 'leave'): void;
};

defineProps<Props>();
const emits = defineEmits<Emits>();

const router = useRouter();

const currentRoom = currentRoomStore();
const { room } = storeToRefs(currentRoom);

const leaveRoom = async () => {
  if (!confirm('Bạn có chắc bạn muốn rời khỏi phòng chơi này chứ?')) {
    return;
  }

  const res = await getOutOfRoomByIdApi(room.value!.ulid);
  if (res === 'UNKNOWN') {
    return showUnexpectedError();
  }

  emits('leave');

  showInfoAlert('Đã rời khỏi phòng thành công', 'Rời khỏi phòng');

  return router.replace({ name: 'rooms' });
};
</script>

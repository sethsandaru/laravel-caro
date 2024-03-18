<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="flex-auto flex-col flex gap-10">
        <div class="flex justify-between">
          <h1 class="text-xl font-semibold leading-6 text-gray-900">
            Phòng: {{ room?.title ?? 'Đang tải dữ liệu' }}
          </h1>

          <button
            type="button"
            class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
            @click="leaveRoom"
          >
            <XCircleIcon
              class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400"
              aria-hidden="true"
            />
            <span>Rời phòng</span>
          </button>
        </div>

        <RoomHeader
          :playing="isPlaying"
          :ready="isSecondPlayerReady"
          @start="startTheGame"
          @ready="markReadyToPlay"
        />
        <CaroPlayground :disabled="!isPlaying" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router';
import { computed, onMounted, ref } from 'vue';
import { getRoomByIdApi } from '@/datasources/api/rooms/getRoomById.api';
import { AxiosError } from 'axios';
import {
  showErrorAlert,
  showInfoAlert,
  showUnexpectedError,
  showWarningAlert,
} from '@/utils/toast';
import { currentRoomStore } from '@/screens/Game/Room/RoomScreen.stores';
import { storeToRefs } from 'pinia';
import RoomHeader from '@/screens/Game/Room/components/RoomHeader.vue';
import CaroPlayground from '@/screens/Game/Room/components/CaroPlayground.vue';
import Echo from 'laravel-echo';
import { LoggedInUser } from '@/datasources/api/auth/getLoggedInUser.api';
import { getEchoInstance } from '@/datasources/websocket/echo';
import { XCircleIcon } from '@heroicons/vue/24/solid';
import { getOutOfRoomByIdApi } from '@/datasources/api/rooms/getOutOfRoomById.api';
import { markAsReadyForRoomByIdApi } from '@/datasources/api/rooms/markAsReadyForRoomById.api';
import { markAsUnReadyForRoomByIdApi } from '@/datasources/api/rooms/markAsUnReadyForRoomById.api';

const route = useRoute();
const router = useRouter();

const currentRoom = currentRoomStore();
const { room, roomChannel } = storeToRefs(currentRoom);

const echo = ref<Echo>(getEchoInstance());

const channelId = computed(() => `playRoom.${room.value?.ulid}`);

const isPlaying = ref(false);
const isSecondPlayerReady = ref(false);

const initWebsocket = () => {
  const channel = echo.value.join(channelId.value);
  currentRoom.setChannel(channel);

  channel
    .here(() => {
      console.log('joined');
    })
    .joining((user: LoggedInUser) => {
      console.log('new user joining', user);
      if (user.ulid === room.value?.createdByUser.ulid) {
        return;
      }

      currentRoom.setSecondUser(user);
    })
    .leaving((user: LoggedInUser) => {
      console.log('user leave channel', user);

      currentRoom.refreshRoom().then((res) => {
        if (res) {
          return;
        }

        router.replace({ name: 'rooms' });
      });
    })
    .listen('SecondPlayerReady', (e) => {
      console.log('ready');
      isSecondPlayerReady.value = true;
    })
    .listen('SecondPlayerUnready', (e) => {
      console.log('unready');
      isSecondPlayerReady.value = false;
    })
    .error((error) => {
      console.error(error);
    });
};

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
  isSecondPlayerReady.value = roomRes.room.status === 'READY_TO_PLAY';
  initWebsocket();
});

const leaveRoom = async () => {
  if (!confirm('Bạn có chắc bạn muốn rời khỏi phòng chơi này chứ?')) {
    return;
  }

  const res = await getOutOfRoomByIdApi(room.value!.ulid);
  if (res === 'UNKNOWN') {
    return showUnexpectedError();
  }

  echo.value.leave(channelId.value);

  showInfoAlert('Đã rời khỏi phòng thành công', 'Rời khỏi phòng');

  return router.replace({ name: 'rooms' });
};

const markReadyToPlay = async () => {
  if (!isSecondPlayerReady.value) {
    const res = await markAsReadyForRoomByIdApi(room.value!.ulid);
    if (!res) {
      return showUnexpectedError();
    }

    isSecondPlayerReady.value = true;
    return;
  }

  const res = await markAsUnReadyForRoomByIdApi(room.value!.ulid);
  if (!res) {
    return showUnexpectedError();
  }

  isSecondPlayerReady.value = false;
  return;
};

const startTheGame = async () => {
  if (!isSecondPlayerReady.value) {
    return showWarningAlert(
      'Người chơi thứ 2 chưa sẵn sàng',
      'Chưa đủ điều kiện chơi'
    );
  }
};
</script>

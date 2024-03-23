<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="flex-auto flex-col flex gap-10">
        <div class="flex justify-between">
          <h1 class="text-xl font-semibold leading-6 text-gray-900">
            Phòng: {{ room?.title ?? 'Đang tải dữ liệu' }}
          </h1>
          <LeaveRoomButton
            :playing="isPlaying"
            @leave="leaveChannel"
          />
        </div>

        <RoomHeader
          :playing="isPlaying"
          :ready="isSecondPlayerReady"
          @start="startTheGame"
          @ready="markReadyToPlay"
        />
        <WinnerAlert v-if="winnerUserId" />
        <CaroPlayground
          v-if="roomChannel"
          :disabled="!isPlaying"
        />
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
import { getOutOfRoomByIdApi } from '@/datasources/api/rooms/getOutOfRoomById.api';
import { markAsReadyForRoomByIdApi } from '@/datasources/api/rooms/markAsReadyForRoomById.api';
import { markAsUnReadyForRoomByIdApi } from '@/datasources/api/rooms/markAsUnReadyForRoomById.api';
import LeaveRoomButton from '@/screens/Game/Room/components/LeaveRoomButton.vue';
import { startGameApi } from '@/datasources/api/rooms/startGame.api';
import { getRoomChannel } from '@/datasources/websocket/roomGameChannel';
import WinnerAlert from '@/screens/Game/Room/components/WinnerAlert.vue';

const route = useRoute();
const router = useRouter();

const currentRoom = currentRoomStore();
const { room, roomChannel } = storeToRefs(currentRoom);

const echo = ref<Echo>(getEchoInstance());

const channelId = computed(() => `playRoom.${room.value?.ulid}`);

const isPlaying = ref(false);
const isSecondPlayerReady = ref(false);

const winnerUserId = ref<string>();

const initWebsocket = () => {
  const channel = getRoomChannel(channelId.value);
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

      winnerUserId.value = undefined;
      currentRoom.setSecondUser(user);
    })
    .leaving((user: LoggedInUser) => {
      console.log('user leave channel', user);

      winnerUserId.value = undefined;
      currentRoom.refreshRoom().then((res) => {
        if (res) {
          return;
        }

        router.replace({ name: 'rooms' });
      });
    });

  channel
    .listen('SecondPlayerReady', () => {
      console.log('SecondPlayerReady');
      isSecondPlayerReady.value = true;
    })
    .listen('SecondPlayerUnready', () => {
      console.log('SecondPlayerUnready');
      isSecondPlayerReady.value = false;
    })
    .listen('NewGameStarted', () => {
      console.log('NewGameStarted');

      isPlaying.value = true;
      winnerUserId.value = undefined;

      showInfoAlert('Trò chơi bắt đầu!!', 'Chơi thôi');
    })
    .listen('GameFinished', (data) => {
      console.log('GameFinished');

      isPlaying.value = false;
      winnerUserId.value = data.winner.ulid;

      showInfoAlert('Đã tìm ra người chiến thắng', 'Game đã kết thúc');
    })
    .error((error: unknown) => {
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

const leaveChannel = () => echo.value.leave(channelId.value);

const startTheGame = async () => {
  if (!isSecondPlayerReady.value) {
    return showWarningAlert(
      'Người chơi thứ 2 chưa sẵn sàng',
      'Chưa đủ điều kiện chơi'
    );
  }

  const res = await startGameApi(room.value!.ulid).catch(() => null);
  if (!res) {
    return showUnexpectedError();
  }
};
</script>

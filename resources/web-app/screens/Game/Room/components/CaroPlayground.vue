<template>
  <div class="mx-auto">
    <div v-if="nextTurnUserId">
      <p class="text-md text-center font-medium text-gray-700 mb-4">
        Tới lượt chơi của
        <strong class="text-pink-500">{{ nextTurnUserName }}</strong> 🎲
      </p>
    </div>
    <div v-if="winnerUserId"></div>

    <table>
      <tbody>
        <tr
          v-for="(columns, rowIndex) in board"
          class="border border-gray-600"
          :key="rowIndex"
        >
          <td
            v-for="(col, columnIndex) in columns"
            class="border w-10 h-10 border-gray-600"
            :class="{
              'cursor-pointer': !disabled,
              'bg-gray-200':
                ((columnIndex % 2 === 0 && rowIndex % 2 !== 0) ||
                  (columnIndex % 2 !== 0 && rowIndex % 2 === 0)) &&
                !disabled,
              'hover:bg-pink-50': !disabled,
              'bg-gray-300': disabled,
            }"
            :key="`${rowIndex}-${columnIndex}`"
            @click="select(rowIndex, columnIndex)"
          >
            <div class="flex justify-center items-center">
              <span v-if="col === 1">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-8 h-8 text-red-500"
                  viewBox="0 0 20 20"
                >
                  <g fill="currentColor">
                    <path
                      d="M7.172 14.243a1 1 0 1 1-1.415-1.415l7.071-7.07a1 1 0 1 1 1.415 1.414z"
                    />
                    <path
                      d="M5.757 7.172a1 1 0 0 1 1.415-1.415l7.07 7.071a1 1 0 1 1-1.414 1.415z"
                    />
                  </g>
                </svg>
              </span>
              <span v-else-if="col === 2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-8 h-8 text-blue-500"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M10 2.5a7.5 7.5 0 1 0 0 15a7.5 7.5 0 0 0 0-15M.5 10a9.5 9.5 0 1 1 19 0a9.5 9.5 0 0 1-19 0"
                    clip-rule="evenodd"
                  />
                </svg>
              </span>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, toRef } from 'vue';
import { currentRoomStore } from '@/screens/Game/Room/RoomScreen.stores';
import { storeToRefs } from 'pinia';
import { getDefaultBoard } from '@/screens/Game/Room/components/CaroPlayground.methods';
import { useUserStore } from '@/stores/user.store';
import { setMoveInBoardApi } from '@/datasources/api/rooms/setMoveInBoard.api';
import { showInfoAlert, showUnexpectedError } from '@/utils/toast';

type Props = {
  disabled?: boolean;
};

const props = defineProps<Props>();
const disabled = toRef(props, 'disabled');

const currentRoom = currentRoomStore();
const { room, roomChannel } = storeToRefs(currentRoom);

const currentUser = useUserStore();
const { user } = storeToRefs(currentUser);

const currentGameId = ref<string>();
const board = ref<number[][]>(getDefaultBoard());

const nextTurnUserId = ref<string>();
const winnerUserId = ref<string>();

const nextTurnUserName = computed(() => {
  if (!nextTurnUserId.value) {
    return '';
  }

  return nextTurnUserId.value === room.value?.createdByUser.ulid
    ? room.value?.createdByUser.name
    : room.value?.secondUser?.name;
});

const winnerUserName = computed(() => {
  if (!winnerUserId.value) {
    return '';
  }

  return winnerUserId.value === room.value?.createdByUser.ulid
    ? room.value?.createdByUser.name
    : room.value?.secondUser?.name;
});

const select = async (rowIdx: number, colIdx: number) => {
  if (
    !board.value || // board does not exist
    disabled.value || // board is disabled
    user.value.ulid !== nextTurnUserId.value // not the turn for this user
  ) {
    return;
  }

  if (board.value[rowIdx][colIdx] !== 0) {
    return;
  }

  board.value[rowIdx][colIdx] =
    room.value?.createdByUser.ulid === nextTurnUserId.value ? 1 : 2;

  await setMoveInBoardApi({
    roomId: room.value!.ulid,
    roomGameId: currentGameId.value!,
    rowIndex: rowIdx,
    colIndex: colIdx,
  }).catch(() => {
    board.value[rowIdx][colIdx] = 0;
    showUnexpectedError();

    return null;
  });
};

const setBoard = (newBoard: number[][]) => {
  board.value = newBoard;
};

onMounted(() => {
  roomChannel.value
    ?.listen('NewGameStarted', (data) => {
      currentGameId.value = data.roomGame.ulid;
      winnerUserId.value = undefined;

      setBoard(data.roomGame.games);
    })
    .listen('NextTurnAvailable', (data) => {
      nextTurnUserId.value = data.user.ulid;

      setBoard(data.roomGame.games);
    })
    .listen('GameFinished', (data) => {
      nextTurnUserId.value = undefined;
      winnerUserId.value = data.winner.ulid;

      setBoard(data.roomGame.games);
    });
});
</script>

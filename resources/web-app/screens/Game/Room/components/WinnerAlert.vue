<template>
  <div class="rounded-md bg-green-50 p-4">
    <div class="flex">
      <div class="flex-shrink-0">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-green-400"
          viewBox="0 0 24 24"
        >
          <g fill="none">
            <path
              d="m12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036c-.01-.003-.019 0-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.016-.018m.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01z"
            />
            <path
              fill="currentColor"
              d="m8.076 7.26l.095.083l8.486 8.485a1 1 0 0 1-.246 1.595l-.116.05L5.91 21.3c-1.952.72-3.853-1.115-3.26-3.064l.05-.146L6.526 7.705a1 1 0 0 1 1.447-.516zm9.031 4.344c.911.048 2.16.24 3.246.892a1 1 0 0 1-.925 1.77l-.104-.055c-.682-.41-1.554-.57-2.322-.61a7.666 7.666 0 0 0-.95.004l-.316.031a1 1 0 0 1-.281-1.98a8.587 8.587 0 0 1 1.652-.053m2.025-2.786a1 1 0 0 1 .116 1.993l-.116.007h-.708a1 1 0 0 1-.116-1.993l.116-.007zM15.95 8.05a1 1 0 0 1 .083 1.32l-.083.094l-1.061 1.061a1 1 0 0 1-1.497-1.32l.083-.094l1.06-1.06a1 1 0 0 1 1.415 0m-2.587-5.266c.448 1.346.208 2.82-.072 3.85a10.11 10.11 0 0 1-.69 1.863a1 1 0 0 1-1.79-.893a8.13 8.13 0 0 0 .55-1.496c.227-.832.341-1.735.166-2.475l-.061-.216a1 1 0 1 1 1.897-.633m5.415 2.438a1 1 0 0 1 0 1.414l-.707.707a1 1 0 1 1-1.414-1.414l.707-.707a1 1 0 0 1 1.414 0"
            />
          </g>
        </svg>
      </div>
      <div class="ml-3">
        <h3 class="text-sm font-medium text-green-800">Trò chơi kết thúc</h3>
        <div class="mt-2 text-sm text-green-700">
          <p>
            Chúc mừng <strong>{{ winnerName }}</strong> đã dành chiến thắng
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, toRef } from 'vue';
import { currentRoomStore } from '@/screens/Game/Room/RoomScreen.stores';
import { storeToRefs } from 'pinia';

type Props = {
  winnerUserId: string;
};

const props = defineProps<Props>();
const winnerUserId = toRef(props, 'winnerUserId');

const currentRoom = currentRoomStore();
const { room } = storeToRefs(currentRoom);

const winnerName = computed(() => {
  return room.value?.createdByUser.ulid === winnerUserId.value
    ? room.value?.createdByUser.name
    : room.value?.secondUser?.name;
});
</script>

<template>
  <div v-if="room">
    <div class="border rounded-xl border-gray-200 bg-white px-4 py-5 sm:px-6">
      <div
        class="-ml-4 -mt-4 flex flex-wrap items-center justify-between sm:flex-nowrap"
      >
        <div class="ml-4 mt-4 flex">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <img
                class="h-12 w-12 rounded-full"
                :src="room.createdByUser.profilePicture"
                alt=""
              />
            </div>
            <div class="ml-4">
              <h3 class="text-base font-semibold leading-6 text-gray-900">
                {{ room.createdByUser.name }}
              </h3>
              <p class="text-sm text-gray-500">
                <a href="#">Chủ phòng</a>
              </p>
            </div>
          </div>
          <div class="flex justify-center items-center ml-10">
            <span class="text-lg font-medium">VS</span>
          </div>
          <div class="flex items-center ml-10">
            <div class="flex-shrink-0">
              <img
                class="h-12 w-12 rounded-full"
                :src="
                  room.secondUser?.profilePicture ??
                  'https://placehold.co/150x150/004400/509669.webp?text=Caro'
                "
                alt=""
              />
            </div>
            <div class="ml-4">
              <h3 class="text-base font-semibold leading-6 text-gray-900">
                {{ room.secondUser?.name ?? 'Đang chờ gia nhập' }}
              </h3>
              <p
                v-if="room.secondUser"
                class="text-sm text-gray-500"
              >
                <a href="#">Chưa sẵn sàng</a>
              </p>
            </div>
          </div>
        </div>
        <div class="ml-4 mt-4 flex flex-shrink-0">
          <button
            v-if="user?.ulid === room.createdByUser.ulid && !playing"
            type="button"
            class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
            @click="$emit('start')"
          >
            <PlayIcon
              class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400"
              aria-hidden="true"
            />
            <span>Bắt đầu</span>
          </button>
          <button
            v-if="user?.ulid === room.secondUser?.ulid"
            type="button"
            class="relative ml-3 inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm ring-1 ring-inset"
            :class="{
              'ring-gray-300 bg-white hover:bg-gray-50 text-gray-900': !ready,
              'bg-pink-500 text-white hover:bg-pink-400 ring-pink-400': ready,
            }"
            @click="$emit('ready')"
          >
            <BoltIcon
              v-if="!ready"
              class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400"
              aria-hidden="true"
            />
            <BoltSlashIcon
              v-else
              class="-ml-0.5 mr-1.5 h-5 w-5 text-white"
              aria-hidden="true"
            />
            <span>Sẵn sàng</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { BoltIcon, BoltSlashIcon, PlayIcon } from '@heroicons/vue/24/solid';
import { currentRoomStore } from '@/screens/Game/Room/RoomScreen.stores';
import { storeToRefs } from 'pinia';
import { useUserStore } from '@/stores/user.store';

type Props = {
  playing?: boolean;
  ready?: boolean;
};

type Emits = {
  (e: 'ready'): void;
  (e: 'start'): void;
};

defineProps<Props>();
defineEmits<Emits>();

const currentRoom = currentRoomStore();
const { room } = storeToRefs(currentRoom);

const currentUser = useUserStore();
const { user } = storeToRefs(currentUser);
</script>

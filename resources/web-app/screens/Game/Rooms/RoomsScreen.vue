<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-gray-900">
          Danh sách phòng chờ
        </h1>
        <p class="mt-2 text-sm text-gray-700">
          Tạo hoặc gia nhập một phòng để chơi.
        </p>
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <CreateRoomButton />
      </div>
    </div>
    <div class="mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <table class="min-w-full divide-y divide-gray-300">
            <thead>
              <tr>
                <th
                  scope="col"
                  class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                >
                  Tên phòng
                </th>
                <th
                  scope="col"
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Trạng thái
                </th>
                <th
                  scope="col"
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Đã chơi
                </th>
                <th
                  scope="col"
                  class="relative py-3.5 pl-3 pr-4 sm:pr-0"
                >
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-if="rooms.length === 0">
                <td
                  colspan="4"
                  class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-center"
                >
                  Chưa có phòng chơi nào!
                </td>
              </tr>
              <tr
                v-for="room in rooms"
                :key="room.ulid"
              >
                <td
                  class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                >
                  {{ room.title }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ getReadableRoomStatus(room.status) }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ room.totalPlayed }} ván
                </td>
                <td
                  class="relative whitespace-nowrap py-4 pl-3 pr-4 text-sm font-medium sm:pr-0"
                >
                  <div class="flex justify-end">
                    <button
                      v-if="
                        user?.ulid !== room.createdByUser?.ulid &&
                        !room.secondUser &&
                        room.status === 'WAITING_FOR_ANOTHER_PLAYER'
                      "
                      type="button"
                      class="block rounded-md bg-pink-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-pink-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-600"
                      @click="joinRoom(room)"
                    >
                      Gia nhập
                    </button>
                    <button
                      v-if="
                        [
                          room.createdByUser.ulid,
                          room.secondUser?.ulid ?? '',
                        ].includes(user?.ulid)
                      "
                      type="button"
                      class="block rounded-md bg-pink-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-pink-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-600"
                      @click="
                        $router.push({
                          name: 'room',
                          params: {
                            id: room.ulid,
                          },
                        })
                      "
                    >
                      Vào lại phòng
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  showInfoAlert,
  showUnexpectedError,
  showWarningAlert,
} from '@/utils/toast';
import { createRoomApi } from '@/datasources/api/rooms/createRoom.api';
import { useRouter } from 'vue-router';
import { onMounted, ref } from 'vue';
import {
  getReadableRoomStatus,
  getRoomsApi,
  Room,
} from '@/datasources/api/rooms/getRooms.api';
import { useUserStore } from '@/stores/user.store';
import { storeToRefs } from 'pinia';
import { joinRoomByIdApi } from '@/datasources/api/rooms/joinRoomById.api';
import CreateRoomButton from '@/screens/Game/Rooms/components/CreateRoomButton.vue';

const router = useRouter();

const currentUser = useUserStore();
const { user } = storeToRefs(currentUser);

const rooms = ref<Room[]>([]);

const loadRooms = async () => {
  const res = await getRoomsApi().catch(() => undefined);
  if (!res) {
    return showUnexpectedError();
  }

  rooms.value = [...res.rooms];
};

const joinRoom = async (room: Room) => {
  const res = await joinRoomByIdApi(room.ulid);

  if (res === 'ALREADY_HAVE_MEMBER_JOINED') {
    showWarningAlert(
      'Đã có người chơi khác tham gia phòng này, xin mời bạn hãy vào phòng khác.',
      'Phòng đã đầy'
    );

    return loadRooms();
  }

  if (res === 'UNKNOWN') {
    showUnexpectedError();

    return;
  }

  showInfoAlert('Vào phòng thành công, đang chuyển hướng,...', 'Thành công');
  router.push({
    name: 'room',
    params: {
      id: room.ulid,
    },
  });
};

onMounted(loadRooms);
</script>

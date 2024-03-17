<template>
  <div>
    <div
      v-if="isLoading"
      class="flex justify-center"
    >
      <span>Đang đăng nhập...</span>
    </div>
    <div
      v-if="hasError"
      class="flex flex-col gap-5"
    >
      <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg
              class="h-5 w-5 text-red-400"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">
              Đã có lỗi xảy ra khi đăng nhập, xin hãy thử lại .
            </h3>
          </div>
        </div>
      </div>
      <div class="mt-10">
        <router-link
          :to="{ name: 'login' }"
          class="text-sm font-semibold leading-7 text-pink-500"
        >
          <span aria-hidden="true">&larr;</span>
          Trở về đăng nhập
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { loginWithGoogleApi } from '@/datasources/api/auth/loginWithGoogle.api';
import { useLoading } from '@/composables/useLoading';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const { isLoading, startLoading, stopLoading } = useLoading();
startLoading();

const hasError = ref(false);
const googleCode = ref('');

onMounted(async () => {
  googleCode.value = String(route.query.code) || '';
  if (!googleCode.value) {
    stopLoading();
    hasError.value = true;
    return;
  }

  const res = await loginWithGoogleApi(googleCode.value);

  stopLoading();

  if (!res) {
    hasError.value = true;
    return;
  }

  router.push({
    name: 'rooms',
  });
});
</script>

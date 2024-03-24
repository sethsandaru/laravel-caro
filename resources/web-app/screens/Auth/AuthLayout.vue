<template>
  <Teleport to="body">
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
      <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img
          class="mx-auto h-64 w-auto"
          src="@/assets/logo.png"
          alt="Laravel Caro by SethSandaru"
        />
        <h2
          class="mt-10 text-center text-3xl font-bold leading-9 tracking-tight text-gray-900"
        >
          Đăng nhập để chơi ngay
        </h2>
      </div>

      <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <router-view />
      </div>
    </div>
    <Footer />
  </Teleport>
</template>
<script setup lang="ts">
import Footer from '@/screens/Common/Footer.vue';
</script>

<template>
  <router-view />
  <notifications
    position="top center"
    classes="vue-notification dov-notification"
  />
</template>

<script setup lang="ts">
import { onMounted } from 'vue';

onMounted(() => {
  const loadingScreen = document.getElementById('loading-screen');
  loadingScreen && loadingScreen.remove();
});
</script>

<style scoped>
:deep(.dov-notification) {
  font-size: 1rem !important;
}
</style>

<template>
  <button
    type="button"
    class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
    :disabled="playing"
    :class="{
      'disabled:bg-gray-300 disabled:hover:bg-gray-300': playing,
      'bg-white hover:bg-gray-50': !playing,
    }"
    @click="$emit('leave')"
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

type Props = {
  playing: boolean;
};

type Emits = {
  (e: 'leave'): void;
};

defineProps<Props>();
defineEmits<Emits>();
</script>

<template>
  <div class="mx-auto">
    <table>
      <tbody>
        <tr
          v-for="rowIndex in 20"
          class="border border-gray-600"
          :key="rowIndex"
        >
          <td
            v-for="columnIndex in 20"
            class="border w-10 h-10 border-gray-600"
            :class="{
              'bg-gray-200 cursor-pointer':
                ((columnIndex % 2 === 0 && rowIndex % 2 !== 0) ||
                  (columnIndex % 2 !== 0 && rowIndex % 2 === 0)) &&
                !disabled,
              'hover:bg-pink-50': !disabled,
              'bg-gray-300': disabled,
            }"
            :key="`${rowIndex}-${columnIndex}`"
          ></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
type Props = {
  disabled?: boolean;
};

defineProps<Props>();
</script>

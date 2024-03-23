<template>
  <div class="mx-auto">
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
import { ref } from 'vue';

type Props = {
  disabled?: boolean;
};

defineProps<Props>();

const board = ref<number[][]>(getDefaultBoard());

let turn = 1;

const select = (rowIdx: number, colIdx: number) => {
  if (board.value[rowIdx][colIdx] !== 0) {
    return;
  }

  board.value[rowIdx][colIdx] = turn;

  if (turn === 1) {
    turn++;
  } else {
    turn--;
  }
};

function getDefaultBoard() {
  const board: number[][] = [];

  for (let i = 0; i < 20; i++) {
    board[i] = [];
    for (let j = 0; j < 20; j++) {
      board[i][j] = 0;
    }
  }

  return board;
}
</script>

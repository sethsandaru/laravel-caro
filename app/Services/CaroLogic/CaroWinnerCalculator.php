<?php

namespace App\Services\CaroLogic;

class CaroWinnerCalculator
{
    /**
     * @param int[][] $board The current game board
     *
     * @return int
     * - 0: no winner
     * - 1: player 1
     * - 2: player 2
     */
    public function calculate(array $board): int
    {
        $rows = count($board);
        $cols = count($board[0]);

        // Check rows, columns, and diagonals
        for ($i = 0; $i < $rows; $i++) {
            // Check rows
            if ($this->isConsecutive($board[$i], 1)) {
                return 1;
            } elseif ($this->isConsecutive($board[$i], 2)) {
                return 2;
            }

            // Check columns
            $columnValues = array_column($board, $i);
            if ($this->isConsecutive($columnValues, 1)) {
                return 1;
            } elseif ($this->isConsecutive($columnValues, 2)) {
                return 2;
            }

            // Check diagonals
            for ($j = 0; $j < $cols; $j++) {
                if ($i + 4 < $rows && $j + 4 < $cols) {
                    $diagonalValues = [];
                    for ($k = 0; $k < 5; $k++) {
                        $diagonalValues[] = $board[$i + $k][$j + $k];
                    }
                    if ($this->isConsecutive($diagonalValues, 1)) {
                        return 1;
                    } elseif ($this->isConsecutive($diagonalValues, 2)) {
                        return 2;
                    }

                    $diagonalValues = [];
                    for ($k = 0; $k < 5; $k++) {
                        $diagonalValues[] = $board[$i + $k][$j + 4 - $k];
                    }
                    if ($this->isConsecutive($diagonalValues, 1)) {
                        return 1;
                    } elseif ($this->isConsecutive($diagonalValues, 2)) {
                        return 2;
                    }
                }
            }
        }

        return 0;
    }

    protected function isConsecutive(array $values, int $number): bool
    {
        $count = 0;
        foreach ($values as $move) {
            if ($move === $number) {
                $count++;

                if ($count >= 5) {
                    return true;
                }
            } else {
                $count = 0;
            }
        }

        return false;
    }
}

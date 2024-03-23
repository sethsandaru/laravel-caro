<?php

namespace App\Services\CaroLogic;

class DefaultGameBoardData
{
    public static function get(): array
    {
        $maxRows = 20;
        $maxCols = 20;

        // 2-dimension array represents the board game
        $board = [];

        for ($rowIdx = 0; $rowIdx < $maxRows; $rowIdx++) {
            $rows = [];

            for ($colIdx = 0; $colIdx < $maxCols; $colIdx++) {
                $rows[] = 0;
            }

            $board[] = $rows;
        }

        return $board;
    }
}

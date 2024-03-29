<?php

namespace App\Services\CaroLogic;

enum SetMoveResult: string
{
    /**
     * The current turn is not available for current logged in user
     */
    case NOT_YOUR_TURN = 'NOT_YOUR_TURN';

    /**
     * If player tries to move on the location that already marked as moved
     *
     * Then we'll have this error
     */
    case CONFLICTED_MOVE = 'CONFLICTED_MOVE';

    /**
     * OK
     */
    case SUCCESS = 'SUCCESS';
}

<?php

namespace App\ToolBox\Management\Sections\Tankers;

use App\Events\Tankers\UpdateTanker;
use App\Tanker;
use App\ToolBox\Management\Tools\Manager;
use LogicException;

class TankersManager extends Manager
{
    /**
     * @param int  $tankerId
     * @param int  $value
     * @param bool $forcedSavingToHistory
     */
    public static function updateValue(int $tankerId, int $value, bool $forcedSavingToHistory = false)
    {
        /** @var Tanker $tanker */
        $tanker = Tanker::find($tankerId);

        if ($tanker === null) {
            throw new LogicException("Tanker with id = $tankerId not found");
        }

        $tanker->setValue($value)->save();

        event(new UpdateTanker($tanker, $forcedSavingToHistory));
    }
}

<?php

namespace App\ToolBox\Management\Sections\Weighing\Sessions;

use App\ToolBox\Management\Sections\Weighing\Sessions\Tools\Dto\WeighingSessionsDataToSync;
use App\ToolBox\Management\Sections\Weighing\Tools\WeighingManager;

class WeighingSessionManager extends WeighingManager
{
    public function sync(array $sessions, array $savedFields = [])
    {
        $dataToSync = new WeighingSessionsDataToSync($sessions, $savedFields);
        $this->syncEntity($dataToSync);
    }
}

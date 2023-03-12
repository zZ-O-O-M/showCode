<?php

namespace App\ToolBox\Management\Sections\Weighing\Tools;

use App\ToolBox\Management\Sections\Weighing\Exception;
use App\ToolBox\Management\Sections\Weighing\RuntimeException;
use App\ToolBox\Management\Tools\Extensions\ExternalResourceManagement;
use App\ToolBox\Management\Tools\Extensions\ResourceManagement;
use App\ToolBox\Management\Tools\Extensions\SynchronizationManagement;
use App\ToolBox\Management\Tools\Manager;
use App\WeighingEntity;

abstract class WeighingManager
{
    use SynchronizationManagement;

    /**
     * @param WeighingEntityDataToSync $dataToSync
     *
     * @return void
     */
    protected function syncEntity(WeighingEntityDataToSync $dataToSync)
    {
        $this->_sync(WeighingEntity::TABLE_NAME, $dataToSync);
    }
}

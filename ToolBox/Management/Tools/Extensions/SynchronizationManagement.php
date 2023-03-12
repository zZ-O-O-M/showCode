<?php

namespace App\ToolBox\Management\Tools\Extensions;

use App\ToolBox\Management\Tools\Dto\DataToSync;
use App\ToolBox\Management\Tools\Manager;
use App\ToolBox\Wizards\SQL;

trait SynchronizationManagement
{
    /**
     * @param string     $table
     * @param DataToSync $dataToSync
     *
     * @return void
     */
    protected function _sync(string $table, DataToSync $dataToSync)
    {
        SQL::insertOnDuplicateKeyUpdate($table, $dataToSync->getData(), $dataToSync->getUpdatedFields());
    }
}

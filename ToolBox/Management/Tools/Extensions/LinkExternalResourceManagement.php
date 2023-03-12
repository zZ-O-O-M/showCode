<?php

namespace App\ToolBox\Management\Tools\Extensions;

use App\ToolBox\Management\Tools\Dto\DataToLink;
use App\ToolBox\Management\Tools\Manager;
use App\ToolBox\Wizards\SQL;

trait LinkExternalResourceManagement
{
    /**
     * @param string     $table
     * @param DataToLink $data
     *
     * @return void
     */
    protected function _linkWithExternal(string $table, DataToLink $data)
    {
        SQL::insertOnDuplicateKeyUpdate($table, $data->getData(), [DataToLink::EXTERNAL_ID]);
    }
}

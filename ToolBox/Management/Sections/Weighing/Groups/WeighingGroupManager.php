<?php

namespace App\ToolBox\Management\Sections\Weighing\Groups;

use App\ToolBox\Management\Sections\Weighing\Groups\Tools\Dto\WeighingGroupsDataToLink;
use App\ToolBox\Management\Sections\Weighing\Groups\Tools\Dto\WeighingGroupsDataToSync;
use App\ToolBox\Management\Sections\Weighing\Tools\WeighingManager;
use App\ToolBox\Management\Tools\Extensions\LinkExternalResourceManagement;
use App\WeighingEntity;

class WeighingGroupManager extends WeighingManager
{
    use LinkExternalResourceManagement;

    /**
     * @param array $groups
     * @param array $savedFields
     *
     * @return void
     */
    public function sync(array $groups, array $savedFields = [])
    {
        $dataToSync = new WeighingGroupsDataToSync($groups, $savedFields);
        $this->syncEntity($dataToSync);
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function linkWithExternal(array $data)
    {
        $dataToLink = new WeighingGroupsDataToLink($data);
        $this->_linkWithExternal(WeighingEntity::TABLE_NAME, $dataToLink);
    }
}

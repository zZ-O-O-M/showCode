<?php

namespace App\ToolBox\Management\Sections\Weighing\Groups\Tools\Dto;

use App\ToolBox\Management\Tools\Dto\DataToLink;
use App\WeighingEntity;
use App\WeighingEntityType;

class WeighingGroupsDataToLink extends DataToLink
{
    protected function prepare()
    {
        parent::prepare();
        $this->addEntityType();
    }

    protected function addEntityType()
    {
        data_fill($this->data, "*." . WeighingEntity::ATTRIBUTE_ENTITY_TYPE_ID, WeighingEntityType::TYPE_GROUP);
    }
}

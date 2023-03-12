<?php

namespace App\ToolBox\Management\Sections\Weighing\Tools;

use App\ToolBox\Management\Tools\Dto\DataToSync;
use App\WeighingEntity;

abstract class WeighingEntityDataToSync extends DataToSync
{
    protected function prepare()
    {
        parent::prepare();
        $this->addEntityType();
    }

    protected function addEntityType()
    {
        data_fill($this->data, "*." . WeighingEntity::ATTRIBUTE_ENTITY_TYPE_ID, $this->entityType());
    }

    /**
     * @return int
     */
    protected abstract function entityType(): int;
}

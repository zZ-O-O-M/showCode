<?php

namespace App\ToolBox\Management\Sections\Weighing\Groups\Tools\Dto;

use App\ToolBox\Management\Sections\Weighing\Tools\WeighingEntityDataToSync;
use App\WeighingEntity;
use App\WeighingEntityType;
use Illuminate\Database\Eloquent\Model;

class WeighingGroupsDataToSync extends WeighingEntityDataToSync
{
    /** @var array */
    protected $safeFields = [
        WeighingEntity::ATTRIBUTE_EXTERNAL_ID,
        WeighingEntity::ATTRIBUTE_ENTITY_TYPE_ID,
        WeighingEntity::ATTRIBUTE_NAME,
        Model::CREATED_AT,
        Model::UPDATED_AT,
    ];

    /** @var array */
    protected $updatedFields = [
        WeighingEntity::ATTRIBUTE_NAME,
        Model::CREATED_AT,
        Model::UPDATED_AT
    ];

    protected function entityType(): int
    {
        return WeighingEntityType::TYPE_GROUP;
    }
}

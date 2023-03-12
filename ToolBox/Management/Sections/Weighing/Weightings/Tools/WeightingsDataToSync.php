<?php

namespace App\ToolBox\Management\Sections\Weighing\Weightings\Tools;

use App\ToolBox\Management\Tools\Dto\DataToSync;
use App\Weighing;

class WeightingsDataToSync extends DataToSync
{
    /** @var array */
    protected $safeFields = [
        Weighing::ATTRIBUTE_EXTERNAL_ID,
        Weighing::ATTRIBUTE_ANIMAL_ID,
        Weighing::ATTRIBUTE_WEIGHT,
        Weighing::ATTRIBUTE_ENTITY_ID
    ];

    /** @var array  */
    protected $updatedFields = [
        Weighing::ATTRIBUTE_ANIMAL_ID,
        Weighing::ATTRIBUTE_WEIGHT,
        Weighing::ATTRIBUTE_ENTITY_ID
    ];
}

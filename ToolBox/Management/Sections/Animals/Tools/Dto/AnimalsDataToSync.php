<?php

namespace App\ToolBox\Management\Sections\Animals\Tools\Dto;

use App\Animal;
use App\ToolBox\Management\Tools\Dto\DataToSync;

class AnimalsDataToSync extends DataToSync
{
    /** @var array */
    protected $safeFields = [
        Animal::ATTRIBUTE_EXTERNAL_ID,
        Animal::ATTRIBUTE_DOB
    ];

    /** @var array */
    protected $updatedFields = [
        Animal::ATTRIBUTE_DOB
    ];
}

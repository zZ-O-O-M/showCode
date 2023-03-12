<?php

namespace App\API\Sections\Weighing\Toolbox\Data;

use App\ToolBox\Data\ApiDataProvider;
use App\ToolBox\Repositories\Weighing\GroupWeightingsRepository;
use App\ToolBox\Repositories\Weighing\SessionWeightingsRepository;
use App\Weighing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class WeightingsDataProvider extends ApiDataProvider
{
    const TYPE_GROUP   = GroupWeightingsRepository::ENTITY_NAME;
    const TYPE_SESSION = SessionWeightingsRepository::ENTITY_NAME;

    /** @var array */
    protected $sort = [
        Weighing::ATTRIBUTE_ANIMAL_ID,
        Weighing::ATTRIBUTE_WEIGHT,
        Model::CREATED_AT,
        Model::UPDATED_AT
    ];

    /** @var array */
    protected $includes = [];

    /** @var array */
    protected $filters = [
        Weighing::ATTRIBUTE_ANIMAL_ID,
        Weighing::ATTRIBUTE_WEIGHT,
        Model::CREATED_AT,
        Model::UPDATED_AT
    ];

    /** @var bool */
    protected $pagination = true;

    /**
     * @param Builder $query
     * @param string  $entityType
     */
    public function __construct(Builder $query, string $entityType)
    {
        parent::__construct($query);
        $this->applyEntityType($entityType);
    }

    /**
     * @param string $type
     *
     * @return void
     */
    protected function applyEntityType(string $type)
    {
        if (!in_array($type, [self::TYPE_GROUP, self::TYPE_SESSION])) {
            throw new InvalidArgumentException("Incorrect entityType");
        }

        $entityIdFieldName = $type . "_id";

        array_unshift($this->sort, $entityIdFieldName);
        array_unshift($this->filters, $entityIdFieldName);
    }
}

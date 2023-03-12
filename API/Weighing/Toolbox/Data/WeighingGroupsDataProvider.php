<?php

namespace App\API\Sections\Weighing\Toolbox\Data;

use App\BaseModel;
use App\ToolBox\Data\ApiDataProvider;
use App\WeighingEntity;
use App\WeighingGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WeighingGroupsDataProvider extends ApiDataProvider
{
    /** @var array */
    protected $sort = [
        BaseModel::ATTRIBUTE_ID,
        WeighingEntity::ATTRIBUTE_NAME,
        Model::CREATED_AT,
        Model::UPDATED_AT,
    ];

    /** @var array */
    protected $includes = [
        WeighingEntity::RELATION_WEIGHING_ENTITY_TYPE,
        WeighingEntity::RELATION_WEIGHINGS
    ];

    /** @var array */
    protected $filters = [
        BaseModel::ATTRIBUTE_ID,
        WeighingEntity::ATTRIBUTE_EXTERNAL_ID,
        WeighingEntity::ATTRIBUTE_NAME,
        Model::CREATED_AT,
        Model::UPDATED_AT,
    ];

    /** @var bool */
    protected $pagination = true;

    /**
     * @param Builder|null $query
     */
    public function __construct(Builder $query = null)
    {
        if ($query === null) {
            $query = WeighingGroup::query();
        }

        parent::__construct($query);
    }
}

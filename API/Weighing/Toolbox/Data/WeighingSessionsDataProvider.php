<?php

namespace App\API\Sections\Weighing\Toolbox\Data;

use App\ToolBox\Data\ApiDataProvider;
use App\WeighingEntity;
use App\WeighingSession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WeighingSessionsDataProvider extends ApiDataProvider
{
    /** @var array */
    protected $sort = [
        WeighingEntity::ATTRIBUTE_EXTERNAL_ID,
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
        WeighingEntity::ATTRIBUTE_EXTERNAL_ID,
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
            $query = WeighingSession::query();
        }

        parent::__construct($query);
    }
}

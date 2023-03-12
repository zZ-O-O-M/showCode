<?php

namespace App\ToolBox\Management\Tools\Dto;

use App\BaseModel;
use Illuminate\Support\Arr;
use LogicException;

class DataToLink
{
    const ID          = 'id';
    const EXTERNAL_ID = 'external_id';

    const FIELDS = [
        'id',
        'external_id'
    ];

    /** @var array */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->prepare();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    protected function prepare()
    {
        $this->prepareFields();
        $this->addUserId();
    }

    protected function prepareFields()
    {
        foreach ($this->data as $key => $datum) {
            $this->data[$key] = Arr::only($datum, self::FIELDS);
            if (!Arr::has($datum, self::FIELDS)) {
                throw new LogicException("Incorrect data structure. All items must contain id and external_id");
            }
        }
    }

    protected function addUserId()
    {
        data_fill($this->data, "*." . BaseModel::ATTRIBUTE_CREATED_BY_USER_ID, auth()->id());
    }
}

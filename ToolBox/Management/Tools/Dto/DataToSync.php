<?php

namespace App\ToolBox\Management\Tools\Dto;

use App\BaseModel;
use Illuminate\Support\Arr;

abstract class DataToSync
{
    /** @var array */
    protected $safeFields = [];

    /** @var array */
    protected $updatedFields = [];

    /** @var array */
    protected $data;

    /** @var array */
    protected $fieldsToSave;

    /**
     * @param array $data
     * @param array $fieldsToSave
     */
    public function __construct(array $data, array $fieldsToSave = [])
    {
        $this->data         = $data;
        $this->fieldsToSave = $fieldsToSave;

        $this->prepare();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getUpdatedFields(): array
    {
        return $this->updatedFields;
    }

    protected function prepare()
    {
        $this->prepareFields();
        $this->prepareUpdatedFieldsValue();
        $this->addUserId();
    }

    # region prepare methods

    protected function prepareUpdatedFieldsValue()
    {
        if (!empty($this->fieldsToSave)) {
            $this->updatedFields = array_intersect($this->updatedFields, $this->fieldsToSave);
        }

        foreach ($this->updatedFields as $neededField) {
            foreach ($this->data as $key => $datum) {
                if (empty($datum[$neededField])) {
                    $this->data[$key][$neededField] = null;
                }
            }
        }
    }

    protected function prepareFields()
    {
        $data         = [];
        $fieldsToSave = empty($this->fieldsToSave) ? $this->safeFields : array_intersect($this->fieldsToSave, $this->safeFields);

        foreach ($this->data as $key => $datum) {
            $data[$key] = Arr::only($datum, $fieldsToSave);
        }

        $this->data = $data;
    }

    protected function addUserId()
    {
        data_fill($this->data, "*." . BaseModel::ATTRIBUTE_CREATED_BY_USER_ID, auth()->id());
    }


    # endregion prepare methods
}

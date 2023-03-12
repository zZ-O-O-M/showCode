<?php

namespace App\ToolBox\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

abstract class AbstractRepository
{
    /** @var array */
    protected $selectFields;

    /** @var */
    protected $resourceFields;

    /** @var string */
    protected $modelClass;

    /** @var string */
    protected $table;

    public function __construct()
    {
        $this->modelClass = $this->modelClass();
        $this->table      = $this->table();

        $this->prepareResourceFields();
        $this->prepareSelectFields();
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->modelClass::query();
    }

    /**
     * @return Builder
     */
    public function select(): Builder
    {
        return $this->query()->select($this->selectFields);
    }

    /**
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->select()->get();
    }

    /**
     * @param array $conditions
     *
     * @return void
     */
    public function archive(array $conditions)
    {
        $this->query()
             ->where($conditions)
             ->update(['archived' => 1, 'archive_date' => date('Y-m-d H:i:s')]);
    }

    # region helpers

    /**
     * @param array $fields
     *
     * @return void
     */
    protected function prepareSelectFields(array $fields = [])
    {
        $resourceFields = $fields ?: $this->resourceFields;

        $result = [];

        foreach ($resourceFields as $name => $as) {
            $result[] = $name . " as " . $as;
        }

        $this->selectFields = $result;
    }

    protected function prepareResourceFields()
    {
        $fields = $this->resourceFields();
        $result = [];

        foreach ($fields as $name => $as) {
            $result[$this->table . ".$name"] = $as;
        }

        $this->resourceFields = $result;
    }

    # endregion helpers

    /**
     * @return string
     */
    protected abstract function modelClass(): string;

    /**
     * @return string
     */
    protected abstract function table(): string;

    /**
     * @return array
     */
    protected abstract function resourceFields(): array;
}

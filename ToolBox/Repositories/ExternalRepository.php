<?php

namespace App\ToolBox\Repositories;

use App\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

abstract class ExternalRepository extends AbstractRepository
{
    /**
     * @return Builder
     */
    public function selectAsExternal(): Builder
    {
        return $this->prepareSelectQueryWithExternalResources()->whereNotNull($this->table . '.external_id');
    }

    /**
     * @return Collection
     */
    public function getAsExternal(): Collection
    {
        return $this->selectAsExternal()->get();
    }

    /**
     * @param int|string|array $externalId
     *
     * @return void
     */
    public function archiveByExternalId($externalId)
    {
        $this->query()->whereIn('external_id', (array)$externalId)
             ->update(['archived' => 1, 'archive_date' => date('Y-m-d H:i:s')]);
    }

    /**
     * @return array
     */
    protected function externalResources(): array
    {
        return [];
    }

    /**
     * @return Builder
     */
    protected function prepareSelectQueryWithExternalResources(): Builder
    {
        $resourceFields = $this->resourceFields;

        $externalResources = $this->externalResources();

        $query = $this->query();

        if (!empty($externalResources)) {
            $query->withoutGlobalScopes(
                [
                    BaseModel::GLOBAL_SCOPE_CREATED_BY_USER_ID,
                    BaseModel::GLOBAL_SCOPE_NOT_ARCHIVED
                ]);
            $query->where(
                [
                    $this->table . "." . BaseModel::ATTRIBUTE_CREATED_BY_USER_ID => auth()->id(),
                    $this->table . "." . BaseModel::ATTRIBUTE_ARCHIVED           => 0
                ]);

            foreach ($this->externalResources() as $fieldName => $settings) {
                $externalResourceTable = $settings['table'];
                $name                  = $settings['name'];

                $query->join($externalResourceTable,
                             $externalResourceTable . ".id",
                             "=", $this->table . "." . $fieldName);
                unset($resourceFields[$this->table . ".$fieldName"]);
                $resourceFields[$externalResourceTable . ".external_id"] = $name;
            }
        }

        unset($resourceFields[$this->table . '.id']);
        unset($resourceFields[$this->table . '.external_id']);

        $resourceFields = Arr::prepend($resourceFields, 'id', $this->table . '.external_id');

        $this->prepareSelectFields($resourceFields);

        return $query->select($this->selectFields);
    }
}

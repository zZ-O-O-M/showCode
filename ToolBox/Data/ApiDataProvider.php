<?php

namespace App\ToolBox\Data;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminatech\DataProvider\DataProvider;

class ApiDataProvider
{
    /** @var array */
    protected $sort = [];

    /** @var string */
    protected $sortDefault = Model::CREATED_AT;

    /** @var array */
    protected $includes = [];

    /** @var array */
    protected $filters = [];

    /** @var bool */
    protected $pagination = false;

    /** @var int */
    protected $perPageDefault = 20;

    /** @var int */
    protected $perPageMax = 200;

    /** @var Builder */
    private $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * @return array|LengthAwarePaginator|Model[]|Collection|object
     */
    public function make()
    {
        $request = Request::instance();

        $dataProvider = DataProvider::new(
            $this->query,
            [
                'pagination' => [
                    'per_page' => [
                        'default' => $this->perPageDefault,
                        'max'     => $this->perPageMax
                    ]
                ]
            ]);

        if (!empty($this->sort)) {
            $dataProvider->sort($this->sort);
        }

        if (!empty($this->sortDefault)) {
            $dataProvider->sortDefault($this->sortDefault);
        }

        if (!empty($this->includes)) {
            $dataProvider->includes($this->includes);
        }

        if (!empty($this->filters)) {
            $dataProvider->filters($this->filters);
        }

        if ($this->pagination) {
            return $dataProvider->paginate($request);
        }
        else {
            return $dataProvider->get($request);
        }
    }

    /**
     * @param array $filters
     * @return $this
     */
    public function filters(array $filters): ApiDataProvider
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @param bool $pagination
     * @return $this
     */
    public function pagination(bool $pagination): ApiDataProvider
    {
        $this->pagination = $pagination;

        return $this;
    }

    /**
     * @param array $sort
     * @return $this
     */
    public function sort(array $sort): ApiDataProvider
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @param string $sortDefault
     * @return $this
     */
    public function sortDefault(string $sortDefault): ApiDataProvider
    {
        $this->sortDefault = $sortDefault;

        return $this;
    }

    /**
     * @param array $include
     * @return $this
     */
    public function includes(array $include): ApiDataProvider
    {
        $this->includes = $include;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function perPageDefault(int $value): ApiDataProvider
    {
        $this->perPageDefault = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function perPageMax(int $value): ApiDataProvider
    {
        $this->perPageMax = $value;

        return $this;
    }
}

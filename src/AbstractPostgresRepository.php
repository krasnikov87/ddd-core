<?php

declare(strict_types=1);

namespace Krasnikov\DDDCore;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

/**
 * Class AbstractUserRepository
 * @package App\Infrastructure\Persists
 */
abstract class AbstractPostgresRepository
{
    /**
     * @var Builder|null
     */
    protected ?Builder $builder = null;

    /**
     * @var Model|null
     */
    protected ?Model $model = null;

    /**
     * @var string|null
     */
    protected ?string $exceptionNotFound = null;


    /**
     * @return Model
     * @throws Exception
     */
    public function getOne(): Model
    {
        $this->makeBuilder();

        try {
            $res = $this->builder->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $this->clearBuilder();
            $class = $this->exceptionNotFound;
            throw new $class($e->getMessage());
        }

        $this->clearBuilder();

        return $res;
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        $this->makeBuilder();

        $res = $this->builder->get();

        $this->clearBuilder();

        return $res;
    }

    /**
     * @param Pagination $pagination
     * @return LengthAwarePaginator
     */
    public function paginate(Pagination $pagination): LengthAwarePaginator
    {
        $this->makeBuilder();

        $res = $this->builder->paginate($pagination->limit(), ['*'], 'page', $pagination->page());

        $this->clearBuilder();

        return $res;
    }

    /**
     * @param Model $model
     * @return Model
     */
    public function store(Model $model): Model
    {
        $this->model = $model;
        $this->model->save();

        return $model;
    }

    /**
     * @param Model $model
     * @throws Exception
     */
    public function delete(Model $model): void
    {
        $this->model = $model;

        $this->model->delete();
    }

    /**
     * @param Sorting $sort
     * @return $this|RepositoryInterface
     */
    public function sort(Sorting $sort): RepositoryInterface
    {
        $this->makeBuilder();

        foreach ($sort->getSort() as $sorting) {
            $this->builder->orderBy($sorting['field'], $sorting['dir']);
        }

        return $this;
    }

    /**
     * @return void
     */
    protected function makeBuilder(): void
    {
        if (!$this->builder) {
            $this->builder = $this->model::query();
        }
    }

    /**
     * @return void
     */
    protected function clearBuilder(): void
    {
        $this->builder = null;
    }
}

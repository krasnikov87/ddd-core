<?php

declare(strict_types=1);

namespace Krasnikov\DDDCore;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class AbstractArrayRepository
 * @package App\Infrastructure\Persists\ArrayRepository
 */
abstract class AbstractArrayRepository
{
    /**
     * @var Collection|Model[]
     */
    protected Collection $result;

    /**
     * @var Collection|Model[]
     */
    protected Collection $seed;

    /**
     * @var string|null
     */
    protected ?string $exceptionNotFound = null;

    /**
     * AbstractArrayRepository constructor.
     */
    public function __construct()
    {
        $this->result = Collection::make();
        $this->seed = Collection::make();
    }

    /**
     * @inheritDoc
     */
    public function getOne(): Model
    {
        $res = $this->result->first();

        if (!$res) {
            $class = $this->exceptionNotFound;
            throw new $class('Model not found');
        }
        return $res;
    }

    /**
     * @inheritDoc
     */
    public function all(): Collection
    {
        return $this->result;
    }

    /**
     * @inheritDoc
     */
    public function paginate(Pagination $pagination): LengthAwarePaginatorInterface
    {
        return new LengthAwarePaginator(
            $this->result->forPage($pagination->page(), $pagination->limit()),
            $this->result->count(),
            $pagination->limit(),
            $pagination->page()
        );
    }

    /**
     * @inheritDoc
     */
    public function store(Model $model): Model
    {
        $this->seed->put($model->id, $model);

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function delete(Model $model): void
    {
        try {
            $this->seed->forget($model->id);
        } catch (Throwable $e) {
            Log::error("Model not found {$model->id}");
        }
    }

    /**
     * @param Sorting $sort
     * @return $this
     */
    public function sort(Sorting $sort): RepositoryInterface
    {
        $result = $this->seed;
        if ($this->result->count()) {
            $result = $this->result;
        }
        $this->result = $result;

        return $this;
    }

    /**
     * @param array $models
     * @return $this|RepositoryInterface
     */
    public function with(array $models): RepositoryInterface
    {
        $result = $this->seed;
        if ($this->result->count()) {
            $result = $this->result;
        }
        $this->result = $result;

        return $this;
    }
}

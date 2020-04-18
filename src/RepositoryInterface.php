<?php

declare(strict_types=1);

namespace Krasnikov\DDDCore;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class AbstractUserRepository
 * @package App\Infrastructure\Persists
 */
interface RepositoryInterface
{
    /**
     * @return Model
     * @throws Exception
     */
    public function getOne(): Model;

    /**
     * @return Collection
     */
    public function all(): Collection;

    /**
     * @param Pagination $pagination
     * @return LengthAwarePaginator
     */
    public function paginate(Pagination $pagination): LengthAwarePaginator;

    /**
     * @param Model $model
     * @return Model
     */
    public function store(Model $model): Model;

    /**
     * @param Model $model
     * @throws Exception
     */
    public function delete(Model $model): void;

    /**
     * @param Sorting $sort
     * @return $this
     */
    public function sort(Sorting $sort): self;
}

<?php

declare(strict_types=1);

namespace Krasnikov\DDDCore;

use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Class Uuid
 * @package App\Domain\Core
 */
trait Uuid
{
    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) RamseyUuid::uuid4();
        });
    }
}

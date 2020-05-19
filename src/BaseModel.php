<?php

declare(strict_types=1);

namespace Krasnikov\DDDCore;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App\Domain\Core
 */
class BaseModel extends Model
{
    use Uuid;

    /**
     * @var bool
     */
    public $incrementing = false;
    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @return array
     */
    public function allowedReferences(): array
    {
        return property_exists($this, 'allowedReferences') ? $this->allowedReferences : [];
    }
}

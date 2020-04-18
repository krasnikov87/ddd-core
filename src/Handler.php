<?php

declare(strict_types=1);

namespace Krasnikov\DDDCore;

/**
 * Interface Handler
 * @package App\Domain\Core
 */
interface Handler
{
    /**
     * @param Command $command
     * @return mixed
     */
    public function handle(Command $command);
}

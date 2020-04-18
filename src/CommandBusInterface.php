<?php

declare(strict_types=1);

namespace Krasnikov\DDDCore;

/**
 * Class CommandBusInterface
 * @package App\Domain
 */
interface CommandBusInterface
{
    /**
     * @param Command $command
     * @return mixed
     */
    public function dispatch(Command $command);
}

<?php

declare(strict_types=1);

namespace Krasnikov\DDDCore;

use Krasnikov\DDDCore\Command;
use Krasnikov\DDDCore\CommandBusInterface;
use Krasnikov\DDDCore\Handler;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;

/**
 * Class CommandBus
 * @package App\Infrastructure\Core
 */
class CommandBus implements CommandBusInterface
{
    /**
     * @var Container
     */
    private Container $container;

    /**
     * CommandBus constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     * @throws BindingResolutionException
     */
    public function dispatch(Command $command)
    {
        /** @var Handler $handler */
        $handler = $this->findHandler(get_class($command) . 'Handler');

        return $handler->handle($command);
    }


    /**
     * @param string $handlerClass
     * @return Handler
     * @throws BindingResolutionException
     */
    private function findHandler(string $handlerClass): Handler
    {
        return  $this->container->make($handlerClass);
    }
}

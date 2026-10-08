<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Double;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class RecordingEventDispatcher implements EventDispatcherInterface
{
    public ?string $lastEventName = null;

    public function dispatch(object $event, ?string $eventName = null): object
    {
        $this->lastEventName = $eventName;

        return $event;
    }
}

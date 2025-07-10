<?php

declare(strict_types=1);

namespace Kernel\Domain\Aggregate;

abstract class EventSourcedAggregateRoot extends AggregateRoot
{
    private int $version = 0;
    
    public function replay(array $events): void
    {
        foreach ($events as $event) {
            $this->apply($event, false);
        }
    }
    
    protected function apply(DomainEvent $event, bool $isNew = true): void
    {
        $method = 'apply' . (new \ReflectionClass($event))->getShortName();
        
        if (method_exists($this, $method)) {
            $this->$method($event);
        }
        
        if ($isNew) {
            $this->raise($event);
        }
        
        $this->version++;
    }
}
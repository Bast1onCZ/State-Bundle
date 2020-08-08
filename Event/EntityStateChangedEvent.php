<?php
declare(strict_types=1);

namespace BastSys\StateBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class StateChangeEvent
 * @package App\ResourceBundle\Event
 * @author mirkl
 */
class EntityStateChangedEvent extends Event
{
    /**
     * @var object
     */
    private $entity;
    /**
     * @var string
     */
    private $prevState;
    /**
     * @var string
     */
    private $newState;

    /**
     * StateChangeEvent constructor.
     * @param object $entity
     * @param string $prevState
     * @param string $newState
     */
    public function __construct(object $entity, string $prevState, string $newState)
    {
        $this->entity = $entity;
        $this->prevState = $prevState;
        $this->newState = $newState;
    }

    /**
     * @return object
     */
    public function getEntity(): object
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getPrevState(): string
    {
        return $this->prevState;
    }

    /**
     * @return string
     */
    public function getNewState(): string
    {
        return $this->newState;
    }
}

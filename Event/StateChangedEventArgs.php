<?php
declare(strict_types=1);

namespace BastSys\StateBundle\Event;

use BastSys\StateBundle\Entity\IState;
use Doctrine\Common\EventArgs;

/**
 * Class StateChangeEventArgs
 *
 * Serves as a doctrine event, which is listened to and is converted into symfony event in StateChangedEventConvertListener
 *
 * @package BastSys\StateBundle\Event
 * @author mirkl
 */
class StateChangedEventArgs extends EventArgs
{
    /**
     * @var IState
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
     * StateChangeEventArgs constructor.
     * @param IState $entity
     * @param string $prevState
     * @param string $newState
     */
    public function __construct(IState $entity, string $prevState, string $newState)
    {
        $this->entity = $entity;
        $this->prevState = $prevState;
        $this->newState = $newState;
    }

    /**
     * @return IState
     */
    public function getEntity(): IState
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

<?php
declare(strict_types=1);

namespace BastSys\StateBundle\EventListener;

use BastSys\StateBundle\Event\StateChangedEventArgs;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class StateChangedEventConvertListener
 *
 * Converts doctrine state changed event to symfony state changed event.
 *
 * @package BastSys\StateBundle\EventListener
 * @author mirkl
 */
class StateChangedEventConvertListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $symfonyDispatcher;

    /**C
     * StateChangedEventConvertListener constructor.
     * @param EventDispatcherInterface $symfonyDispatcher
     */
    public function __construct(EventDispatcherInterface $symfonyDispatcher)
    {
        $this->symfonyDispatcher = $symfonyDispatcher;
    }

    /**
     * @param StateChangedEventArgs $args
     */
    public function stateChanged(StateChangedEventArgs $args) {
        $symfonyEvent = $args->getEntity()->createStateChangedEvent(
            $args->getPrevState(),
            $args->getNewState()
        );

        $this->symfonyDispatcher->dispatch($symfonyEvent, get_class($symfonyEvent));
    }
}

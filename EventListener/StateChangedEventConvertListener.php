<?php
declare(strict_types=1);

namespace App\ResourceBundle\EventListener;

use App\ResourceBundle\Event\StateChangedEventArgs;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class StateChangedEventConvertListener
 *
 * Converts doctrine state changed event to symfony state changed event.
 *
 * @package App\ResourceBundle\EventListener
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

<?php
declare(strict_types=1);

namespace BastSys\StateBundle\Entity;

use App\ResourceBundle\Event\StateChangedEventArgs;
use BastSys\StateBundle\Event\EntityStateChangedEvent;
use BastSys\StateBundle\Exception\StateChangeException;
use BastSys\StateBundle\Exception\WrongStateException;
use BastSys\StateBundle\Structure\StateTransition;
use BastSys\UtilsBundle\Entity\EntityManagerAware\TEntityManagerAware;

/**
 * Trait TState
 *
 * Class using TState must implement IState interface
 *
 * @package BastSys\StateBundle\Entity
 * @author mirkl
 */
abstract class TState implements IState
{
    use TEntityManagerAware;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    /**
     * @var string|null defined only if a state is being changed
     */
    private $currentSettingState = null;

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @param array $params necessary information to change state
     * @throws StateChangeException
     * @throws \Throwable
     */
    public function setState(string $state, array $params = []): void
    {
        $availableStates = $this->getAvailableStates();
        if (!in_array($state, $availableStates)) {
            $class = get_class($this);
            throw new StateChangeException("Unavailable state transition '$this->state' > '$state' in $class", 400);
        }

        if ($this->currentSettingState === $state) {
            // Prevent duplicate state setting. Can happen when two entities listen to state change of each other.
            return;
        }

        $this->currentSettingState = $state;

        $em = $this->entityManager;
        if(!$em) {
            throw new \LogicException('Cannot set state for entity that is not managed by entity manager. You must persist the entity first.', 500);
        }

        $eventManager = $em->getEventManager();
        $this->processStateChange($state, $params);

        $prevState = $this->state;
        $this->state = $state;

        try {
            $eventManager->dispatchEvent(
                StateChangedEventArgs::class,
                new StateChangedEventArgs($this, $prevState, $state)
            );
        } catch (\Throwable $ex) {
            $this->state = $prevState;
            $this->currentSettingState = null;
            throw $ex;
        }

        $this->currentSettingState = null;
    }

    /**
     * Gets available states that can be accessed from the current state
     *
     * @return string[]
     * @todo this code might stop working if all state transitions are skippable
     *
     */
    public final function getAvailableStates(): array
    {
        $availableStates = [];

        /**
         * @param string $state
         */
        $processSubAvailableTransitions = function (string $state) use (&$processSubAvailableTransitions, &$availableStates) {
            /** @var StateTransition[] $transitions */
            $transitions = $this->getStateTransitions($state); // get transitions connected to this state

            foreach ($transitions as $transition) {
                $transitionState = $transition->__toString();

                // if transition is not already allowed, then it is processed
                if (!in_array($transitionState, $availableStates)) {
                    if ($transition->canPass($this)) {
                        // transition is available
                        $availableStates[] = $transitionState;
                    }
                    if ($transition->isSkippable()) {
                        // transition may be skipped => need to process all next transitions
                        $processSubAvailableTransitions($transitionState);
                    }
                }
            }
        };

        $processSubAvailableTransitions($this->state);

        return $availableStates;
    }

    /**
     * @param string $state
     * @return StateTransition[]
     */
    public abstract function getStateTransitions(string $state): array;

    /**
     * Is called to change other entity parameters before the change to new state is confirmed.
     * Current state can be still accessed by TState::getState() method.
     * Use params to pass necessary information to change state.
     *
     * @param string $newState
     * @param array $params
     */
    protected abstract function processStateChange(string $newState, array $params = []): void;

    /**
     * Checks whether current state is an allowed state.
     * If not, an exception is thrown.
     * Use this method for state security inside entity (and dependent entities).
     *
     * @param string|string[] $allowedState allowed state or states
     * @throws WrongStateException thrown when current state is not the same as the expected one
     */
    public function checkState($allowedState): void
    {
        if (!$this->hasState($allowedState)) {
            throw new WrongStateException($this->state, $allowedState);
        }
    }

    /**
     * Checks whether current state is an allowed state.
     * Use this method for state security inside entity (and dependent entities).
     *
     * @param string|string[] $allowedState allowed state or states
     * @return bool
     */
    public function hasState($allowedState): bool
    {
        if (is_string($allowedState)) {
            return $this->state === $allowedState;
        } else if (is_array($allowedState)) {
            return in_array($this->state, $allowedState, true);
        } else {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @param string $defaultState
     * @throws StateChangeException
     */
    protected function initState(string $defaultState)
    {
        if(!($this instanceof IState)) {
            throw new \LogicException(get_class($this) .' does not implement '. IState::class .' interface');
        }
        if ($this->state) {
            throw new StateChangeException('State is already set. Use TState::setState instead');
        }
        $this->state = $defaultState;
    }

    /**
     * Creates symfony event of state change.
     * Override this method to create a custom state changed event.
     * This event must however extend EntityStateChangedEvent
     *
     * @param string $prevState
     * @param string $newState
     * @return EntityStateChangedEvent
     */
    public function createStateChangedEvent(string $prevState, string $newState): EntityStateChangedEvent {
        return new EntityStateChangedEvent($this, $prevState,  $newState);
    }
}

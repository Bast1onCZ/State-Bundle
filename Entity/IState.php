<?php
declare(strict_types=1);

namespace BastSys\StateBundle\Entity;

use BastSys\StateBundle\Event\EntityStateChangedEvent;
use BastSys\StateBundle\Exception\StateChangeException;
use BastSys\StateBundle\Exception\WrongStateException;
use BastSys\StateBundle\Structure\StateTransition;

/**
 * Trait TState
 * @package BastSys\StateBundle\Entity
 * @author mirkl
 */
interface IState
{
    /**
     * @return string
     */
    public function getState(): string;

    /**
     * @param string $state
     * @param array $params necessary information to change state
     * @throws StateChangeException
     * @throws \Throwable
     */
    public function setState(string $state, array $params = []): void;

    /**
     * Gets available states that can be accessed from the current state
     *
     * @return string[]
     */
    public function getAvailableStates(): array;

    /**
     * @param string $state
     * @return StateTransition[]
     */
    public function getStateTransitions(string $state): array;

    /**
     * Checks whether current state is an allowed state.
     * If not, an exception is thrown.
     * Use this method for state security inside entity (and dependent entities).
     *
     * @param string|string[] $allowedState allowed state or states
     * @throws WrongStateException thrown when current state is not the same as the expected one
     */
    public function checkState($allowedState): void;

    /**
     * Checks whether current state is an allowed state.
     * Use this method for state security inside entity (and dependent entities).
     *
     * @param string|string[] $allowedState allowed state or states
     * @return bool
     */
    public function hasState($allowedState): bool;

    /**
     * Creates symfony event of state change.
     * Override this method to create a custom state changed event.
     * This event must however extend EntityStateChangedEvent
     *
     * @param string $prevState
     * @param string $newState
     * @return EntityStateChangedEvent
     */
    public function createStateChangedEvent(string $prevState, string $newState): EntityStateChangedEvent;
}

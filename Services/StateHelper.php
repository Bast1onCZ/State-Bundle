<?php

namespace BastSys\StateBundle\Services;

use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Class StateHelper
 * @package BastSys\StateBundle\Services
 */
class StateHelper
{
    /**
     * Gets all available states through current available transitions
     *
     * @param WorkflowInterface $workflow
     * @param object $subject
     * @return array|string[]
     */
    public static function getAvailableStates(WorkflowInterface $workflow, object $subject): array {
        /** @var string[] $availableStates */
        $availableStates = [];

        foreach ($workflow->getEnabledTransitions($subject) as $transition) {
            foreach ($transition->getTos() as $availableState) {
                $availableStates[] = $availableState;
            }
        }

        return array_unique($availableStates);
    }

    /**
     * Gets transition that is currently available to get subject to targetState
     *
     * @param WorkflowInterface $workflow
     * @param object $subject
     * @param string $targetState
     * @return Transition|null
     */
    public static function getTransitionToState(WorkflowInterface $workflow, object $subject, string $targetState): ?Transition {
        foreach($workflow->getEnabledTransitions($subject) as $transition) {
            if(in_array($targetState, $transition->getTos(), true)) {
                return $transition;
            }
        }

        return null;
    }
}

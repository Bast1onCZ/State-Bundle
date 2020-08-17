<?php
declare(strict_types=1);

namespace BastSys\StateBundle\Structure;

/**
 * Class StateTransition
 * @package BastSys\StateBundle\Structure\Entity\State
 * @author mirkl
 */
class StateTransition
{
    /**
     * @var string
     */
    private $targetState;
    /**
     * @var callable
     */
    private $condition;
    /**
     * @var bool
     */
    private $skippable;

    /**
     * StateTransition constructor.
     * @param string $targetState
     * @param callable|null $condition
     * @param bool $skippable Indicates whether this state can be skipped while changing to next state which is available
     */
    public function __construct(string $targetState, bool $skippable = false, callable $condition = null)
    {
        $this->targetState = $targetState;
        $this->skippable = $skippable;
        $this->condition = $condition;
    }

    /**
     * @param object $entity
     * @return bool
     */
    public function canPass(object $entity) {
        $condition = $this->condition;

        return !$condition || $condition($entity);
    }

    /**
     * @return bool
     */
    public function isSkippable(): bool
    {
        return $this->skippable;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->targetState;
    }
}

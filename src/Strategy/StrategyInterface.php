<?php
namespace Nubs\Sensible\Strategy;

/**
 * A strategy pattern interface for finding commands.
 */
interface StrategyInterface
{
    /**
     * Return the path to the command if this strategy can find one.
     *
     * @return string|null The path to the command if found, null otherwise.
     */
    public function get();
}

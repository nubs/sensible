<?php
namespace Nubs\Sensible\CommandFactory;

use Exception;
use Nubs\Sensible\Strategy\StrategyInterface;

/**
 * Defines common methods for a command factory to use.
 */
trait CommandFactoryTrait
{
    /**
     * Execute the strategy and return the command.
     *
     * @param \Nubs\Sensible\Strategy\StrategyInterface $strategy The strategy
     *     to locate a command with.
     * @return string The command to execute.
     * @throws \Exception if the strategy fails to find a command.
     */
    protected function getCommand(StrategyInterface $strategy)
    {
        $command = $strategy->get();
        if ($command === null) {
            throw new Exception('Failed to locate a sensible command');
        }

        return $command;
    }
}

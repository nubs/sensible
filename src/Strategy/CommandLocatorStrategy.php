<?php
namespace Nubs\Sensible\Strategy;

use Nubs\Which\Locator as CommandLocator;

/**
 * Uses a command locator to check a list of commands for one that exists.
 */
class CommandLocatorStrategy implements StrategyInterface
{
    /** @type array The list of commands to try locating. */
    private $_commands;

    /** @type \Nubs\Which\Locator The command locator. */
    private $_commandLocator;

    /**
     * Initialize the strategy to locate a suitable command from a list.
     *
     * @param string[] $commands A list of commands to look for.
     * @param \Nubs\Which\Locator The command locator.
     */
    public function __construct(array $commands, CommandLocator $commandLocator)
    {
        $this->_commands = $commands;
        $this->_commandLocator = $commandLocator;
    }

    /**
     * Returns the path to the first command found in the list.
     *
     * @return string|null The located command, or null if none could be found.
     */
    public function get()
    {
        foreach ($this->_commands as $command) {
            $location = $this->_commandLocator->locate(basename($command));
            if ($location !== null) {
                return $location;
            }
        }

        return null;
    }
}

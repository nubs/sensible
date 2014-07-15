<?php
namespace Nubs\Sensible\Strategy;

use Habitat\Environment\Environment;

/**
 * Checks a given environment variable and returns its value if not null.
 */
class EnvironmentVariableStrategy implements StrategyInterface
{
    /** @type string The environment variable name. */
    private $_name;

    /** @type \Habitat\Environment\Environment The environment wrapper. */
    private $_environment;

    /**
     * Initialize the strategy that checks an environment variable for a
     * command.
     *
     * @param string $name The environment variable name to lookup.
     * @param \Habitat\Environment\Environment $environment The environment
     *     variable wrapper.  Defaults to null which just uses PHP's built-in
     *     getenv.
     */
    public function __construct($name, Environment $environment = null)
    {
        $this->_name = $name;
        $this->_environment = $environment;
    }

    /**
     * Returns the command as found in the environment variable.
     *
     * @return string|null The command, or null if the environment variable
     *     wasn't set.
     */
    public function get()
    {
        $result = $this->_environment ? $this->_environment->getenv($this->_name) : getenv($this->_name);

        return $result !== false ? $result : null;
    }
}

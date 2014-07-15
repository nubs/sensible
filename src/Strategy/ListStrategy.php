<?php
namespace Nubs\Sensible\Strategy;

/**
 * Executes each strategy in turn until one of them returns a non-null result.
 */
class ListStrategy implements StrategyInterface
{
    /** @type \Nubs\Sensible\StrategyInterface[] The strategies to use. */
    private $_strategies;

    /**
     * Initialize the strategy to wrap a list of alternate strategies.
     *
     * @param \Nubs\Sensible\StrategyInterface[] $strategies The strategies to
     *     use.
     */
    public function __construct(array $strategies)
    {
        $this->_strategies = $strategies;
    }

    /**
     * Returns the result of the first successful strategy.
     *
     * @return string|null The command as located by a strategy or null if no
     *     strategies found a command to use.
     */
    public function get()
    {
        foreach ($this->_strategies as $strategy) {
            $result = $strategy->get();
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }
}

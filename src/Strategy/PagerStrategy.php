<?php
namespace Nubs\Sensible\Strategy;

use Habitat\Environment\Environment;
use Nubs\Which\Locator as CommandLocator;

/**
 * Uses the EnvironmentVariableStrategy (PAGER) and CommandLocatorStrategy to
 * create a Pager.
 */
class PagerStrategy extends ListStrategy implements StrategyInterface
{
    /**
     * Initialize the pager strategy.
     *
     * @param string|string[] $pagers The names to the potential pagers.
     *     The first command in the list that can be located will be used.
     * @param \Nubs\Which\Locator $commandLocator The command locator.  This
     *     helps locate commands using PATH.
     * @param \Habitat\Environment\Environment $environment The environment
     *     variable wrapper.  Defaults to null, which just uses the built-in
     *     getenv.
     */
    public function __construct($pagers, CommandLocator $commandLocator, Environment $environment = null)
    {
        parent::__construct([new EnvironmentVariableStrategy('PAGER', $environment), new CommandLocatorStrategy($pagers, $commandLocator)]);
    }
}

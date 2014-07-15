<?php
namespace Nubs\Sensible\CommandFactory;

use Exception;
use Habitat\Environment\Environment;
use Nubs\Sensible\Pager;
use Nubs\Sensible\Strategy\PagerStrategy;
use Nubs\Which\Locator as CommandLocator;

/**
 * Uses the PagerStrategy to locate a pager.
 */
class PagerFactory
{
    use CommandFactoryTrait;

    /** @type \Nubs\Sensible\Strategy\PagerFactory The pager strategy. */
    private $_strategy;

    /**
     * Create the pager.
     *
     * @api
     * @param \Nubs\Which\Locator $commandLocator The command locator.  This
     *     helps locate commands using PATH.
     * @param string[] $pagers The names to the potential pagers.  The first
     *     command in the list that can be located will be used.
     * @param \Habitat\Environment\Environment $environment The environment
     *     variable wrapper.  Defaults to null, which just uses the built-in
     *     getenv.
     */
    public function __construct(CommandLocator $commandLocator, array $pagers = ['sensible-pager', 'less', 'more'], Environment $environment = null)
    {
        $this->_strategy = new PagerStrategy($pagers, $commandLocator, $environment);
    }

    /**
     * Create the pager object using the strategy.
     *
     * @api
     * @return \Nubs\Sensible\Pager The created pager object.
     * @throws \Exception if no pager can be found.
     */
    public function create()
    {
        return new Pager($this->getCommand($this->_strategy));
    }
}

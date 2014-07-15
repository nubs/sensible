<?php
namespace Nubs\Sensible\CommandFactory;

use Exception;
use Nubs\Sensible\Browser;
use Nubs\Sensible\Strategy\BrowserStrategy;
use Nubs\Which\Locator as CommandLocator;

/**
 * Uses the BrowserStrategy to locate an browser.
 */
class BrowserFactory
{
    /** @type \Nubs\Sensible\Strategy\BrowserFactory The browser strategy. */
    private $_strategy;

    /**
     * Create the browser.
     *
     * @api
     * @param \Nubs\Which\Locator $commandLocator The command locator.  This
     *     helps locate commands using PATH.
     * @param string[] $browsers The names to the potential browsers.  The first
     *     command in the list that can be located will be used.
     */
    public function __construct(CommandLocator $commandLocator, array $browsers = ['sensible-browser', 'firefox', 'chromium-browser', 'chrome', 'elinks'])
    {
        $this->_strategy = new BrowserStrategy($browsers, $commandLocator);
    }

    /**
     * Create the browser object using the strategy.
     *
     * @api
     * @return \Nubs\Sensible\Browser The created browser object.
     * @throws \Exception if no browser can be found.
     */
    public function create()
    {
        $browser = $this->_strategy->get();
        if ($browser === null) {
            throw new Exception('Failed to locate a sensible command');
        }

        return new Browser($browser);
    }
}

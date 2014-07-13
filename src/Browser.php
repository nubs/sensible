<?php
namespace Nubs\Sensible;

use Nubs\Which\Locator as CommandLocator;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Provides access to the user's preferred browser command.
 *
 * Browsers are found by a configurable list.
 */
class Browser
{
    /** @type string[] The paths to potential browsers. */
    protected $_browserPaths;

    /** @type \Nubs\Which\Locator The command locator. */
    protected $_commandLocator;

    /**
     * Initialize the browser loader.
     *
     * @api
     * @param \Nubs\Which\Locator $commandLocator The command locator.  This
     *     helps locate commands using PATH.
     * @param string|string[] $browserPaths The names to the potential browsers.
     *     The first command in the list that can be located will be used.
     */
    public function __construct(CommandLocator $commandLocator, $browserPaths = ['sensible-browser', 'firefox', 'chromium-browser', 'chrome', 'elinks'])
    {
        $this->_commandLocator = $commandLocator;
        $this->_browserPaths = array_values((array)$browserPaths);
    }

    /**
     * Get the path to the user's preferred browser.
     *
     * @api
     * @return string|null The path to the user's preferred browser if one is
     *     found.
     */
    public function get()
    {
        foreach ($this->_browserPaths as $browserPath) {
            $location = $this->_commandLocator->locate(basename($browserPath));
            if ($location !== null) {
                return $location;
            }
        }

        return null;
    }

    /**
     * View the given URI using the symfony process builder to build the symfony
     * process to execute.
     *
     * @api
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder The
     *     process builder.
     * @param string $uri The URI to view.
     * @return \Symfony\Component\Process\Process The already-executed process.
     */
    public function viewURI(ProcessBuilder $processBuilder, $uri)
    {
        $proc = $processBuilder->setPrefix($this->get())->setArguments([$uri])->getProcess();
        $proc->setTty(true)->run();

        return $proc;
    }
}

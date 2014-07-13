<?php
namespace Nubs\Sensible;

use Nubs\Which\Locator as CommandLocator;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Provides access to the user's preferred browser command.
 *
 * If the user has a "sensible-browser" command, that takes preference to other
 * behavior.  If that does not exist then a default browser is used.
 */
class Browser
{
    /** @type string[] The paths to default browser choices to use if no alternative is found. */
    protected $_defaultBrowserPath = array('/usr/bin/sensible-browser', '/usr/bin/firefox', '/usr/bin/chromium-browser', '/usr/bin/chrome', '/usr/bin/elinks');

    /** @type \Nubs\Which\Locator The command locator. */
    protected $_commandLocator;

    /**
     * Initialize the browser loader and configure the options
     *
     * @api
     * @param \Nubs\Which\Locator $commandLocator The command locator.  This
     *     helps locate commands using PATH.
     * @param array $options {
     *     @type string|string[] $defaultBrowserPath The paths to the default
     *         browser choices to use if no alternative is found.  The first
     *         browser in the list that can be located will be used.  Defaults
     *         to ['/usr/bin/sensible-browser', '/usr/bin/firefox',
     *         '/usr/bin/chromium-browser', '/usr/bin/chrome',
     *         '/usr/bin/elinks'].
     * }
     */
    public function __construct(CommandLocator $commandLocator, array $options = array())
    {
        $this->_commandLocator = $commandLocator;

        if (isset($options['defaultBrowserPath'])) {
            $this->_defaultBrowserPath = array_values((array)$options['defaultBrowserPath']);
        }
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
        foreach ($this->_defaultBrowserPath as $browserPath) {
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
        $proc = $processBuilder->setPrefix($this->get())->setArguments(array($uri))->getProcess();
        $proc->setTty(true)->run();

        return $proc;
    }
}

<?php
namespace Nubs\Sensible;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Provides access to the user's preferred browser command.
 *
 * If the user has a "sensible-browser" command, that takes preference to other
 * behavior.  If that does not exist then a default browser is used (/usr/bin/elinks by
 * default).
 */
class Browser
{
    /** @type string The path to debian's sensible-browser. */
    protected $_sensibleBrowserPath;

    /** @type string[] The paths to default browser choices to use if no alternative is found. */
    protected $_defaultBrowserPath = array('/usr/bin/firefox', '/usr/bin/chromium-browser', '/usr/bin/chrome', '/usr/bin/elinks');

    /** @type \Nubs\Which\Locator The command locator. */
    protected $_commandLocator;

    /**
     * Initialize the browser loader and configure the options
     *
     * @api
     * @param array $options {
     *     @type string $sensibleBrowserPath The path to debian's
     *         sensible-browser.  Defaults to '/usr/bin/sensible-browser'.
     *     @type string|string[] $defaultBrowserPath The paths to the default
     *         browser choices to use if no alternative is found.  The first
     *         browser in the list that can be located will be used.  Defaults
     *         to ['/usr/bin/firefox', '/usr/bin/chromium-browser',
     *         '/usr/bin/chrome', '/usr/bin/elinks'].
     *     @type \Nubs\Which\Locator $commandLocator The command locator.  When
     *         provided, this helps locate commands using PATH rather than hard-
     *         coded locations.
     * }
     */
    public function __construct(array $options = array())
    {
        $this->_sensibleBrowserPath = isset($options['sensibleBrowserPath']) ? $options['sensibleBrowserPath'] : '/usr/bin/sensible-browser';

        if (isset($options['defaultBrowserPath'])) {
            $this->_defaultBrowserPath = array_values((array)$options['defaultBrowserPath']);
        }

        if (isset($options['commandLocator'])) {
            $this->_commandLocator = $options['commandLocator'];
        }
    }

    /**
     * Get the path to the user's preferred browser.
     *
     * @api
     * @return string The path to the user's preferred browser.
     */
    public function get()
    {
        $sensibleBrowser = $this->_getSensibleBrowser();
        if ($sensibleBrowser !== null) {
            return $sensibleBrowser;
        }

        return $this->_getDefaultBrowser();
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

    /**
     * Gets the path to the sensible browser using the locator if it is set.
     *
     * @return string|null The path to the sensible browser or null if it isn't
     *     available.
     */
    protected function _getSensibleBrowser()
    {
        if ($this->_commandLocator) {
            return $this->_commandLocator->locate(basename($this->_sensibleBrowserPath));
        }

        return is_executable($this->_sensibleBrowserPath) ? $this->_sensibleBrowserPath : null;
    }

    /**
     * Gets the path to the default browser using the locator if it is set.
     *
     * @return string|null The path to the default browser.
     */
    protected function _getDefaultBrowser()
    {
        if ($this->_commandLocator) {
            foreach ($this->_defaultBrowserPath as $browserPath) {
                $location = $this->_commandLocator->locate(basename($browserPath));
                if ($location !== null) {
                    return $location;
                }
            }
        }

        return empty($this->_defaultBrowserPath) ? null : $this->_defaultBrowserPath[count($this->_defaultBrowserPath) - 1];
    }
}

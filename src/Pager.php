<?php
namespace Nubs\Sensible;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Provides access to the user's preferred pager command.
 *
 * If the user has a "sensible-pager" command, that takes preference to other
 * behavior.  If that does not exist, then the "PAGER" environment variable is
 * used.  If that does not exist, then a default pager is used (/bin/more by
 * default).
 */
class Pager
{
    /** @type string The path to debian's sensible-pager. */
    protected $_sensiblePagerPath;

    /** @type string The path to the default pager to use if no alternative is found. */
    protected $_defaultPagerPath;

    /** @type \Habitat\Environment\Environment The environment variable wrapper. */
    protected $_environment;

    /** @type \Nubs\Which\Locator The command locator. */
    protected $_commandLocator;

    /**
     * Initialize the pager loader and configure the options
     *
     * @api
     * @param array $options {
     *     @type string $sensiblePagerPath The path to debian's sensible-pager.
     *         Defaults to '/usr/bin/sensible-pager'.
     *     @type string $defaultPagerPath The path to the default pager to use
     *         if no alternative is found.  Defaults to '/bin/more'.
     *     @type \Habitat\Environment\Environment $environment The environment
     *         variable wrapper.  Defaults to null, which just uses the built-in
     *         getenv.
     *     @type \Nubs\Which\Locator $commandLocator The command locator.  When
     *         provided, this helps locate commands using PATH rather than hard-
     *         coded locations.
     * }
     */
    public function __construct(array $options = array())
    {
        $this->_sensiblePagerPath = isset($options['sensiblePagerPath']) ?  $options['sensiblePagerPath'] : '/usr/bin/sensible-pager';
        $this->_defaultPagerPath = isset($options['defaultPagerPath']) ?  $options['defaultPagerPath'] : '/bin/more';

        if (isset($options['environment'])) {
            $this->_environment = $options['environment'];
        }

        if (isset($options['commandLocator'])) {
            $this->_commandLocator = $options['commandLocator'];
        }
    }

    /**
     * Get the path to the user's preferred pager.
     *
     * @api
     * @return string The path to the user's preferred pager.
     */
    public function get()
    {
        $sensiblePager = $this->_getSensiblePager();
        if ($sensiblePager !== null) {
            return $sensiblePager;
        }

        $pager = $this->_environment ? $this->_environment->getenv('PAGER') : getenv('PAGER');

        return $pager ?: $this->_getDefaultPager();
    }

    /**
     * View the given file using the symfony process builder to build the
     * symfony process to execute.
     *
     * @api
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder The process builder.
     * @param string $filePath The path to the file to view.
     * @return \Symfony\Component\Process\Process The already-executed process.
     */
    public function viewFile(ProcessBuilder $processBuilder, $filePath)
    {
        $proc = $processBuilder->setPrefix($this->get())->setArguments(array($filePath))->getProcess();
        $proc->setTty(true)->run();

        return $proc;
    }

    /**
     * View the given data using the symfony process builder to build the
     * symfony process to execute.
     *
     * @api
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder The process builder.
     * @param string $data The data to view.
     * @return \Symfony\Component\Process\Process The already-executed process.
     */
    public function viewData(ProcessBuilder $processBuilder, $data)
    {
        $proc = $processBuilder->setPrefix($this->get())->setInput($data)->getProcess();
        $proc->setTty(true)->run();

        return $proc;
    }

    /**
     * Gets the path to the sensible pager using the locator if it is set.
     *
     * @return string|null The path to the sensible pager or null if it isn't
     *     available.
     */
    protected function _getSensiblePager()
    {
        if ($this->_commandLocator) {
            return $this->_commandLocator->locate(basename($this->_sensiblePagerPath));
        }

        return is_executable($this->_sensiblePagerPath) ? $this->_sensiblePagerPath : null;
    }

    /**
     * Gets the path to the default pager using the locator if it is set.
     *
     * @return string|null The path to the default pager.
     */
    protected function _getDefaultPager()
    {
        if ($this->_commandLocator) {
            return $this->_commandLocator->locate(basename($this->_defaultPagerPath)) ?: $this->_defaultPagerPath;
        }

        return $this->_defaultPagerPath;
    }
}

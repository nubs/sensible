<?php
namespace Nubs\Sensible;

use Nubs\Which\Locator as CommandLocator;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Provides access to the user's preferred pager command.
 *
 * Pagers are found by a configurable list.
 */
class Pager
{
    /** @type string[] The paths to potential pagers. */
    protected $_pagerPaths;

    /** @type \Habitat\Environment\Environment The environment variable wrapper. */
    protected $_environment;

    /** @type \Nubs\Which\Locator The command locator. */
    protected $_commandLocator;

    /**
     * Initialize the pager loader and configure the options
     *
     * @api
     * @param \Nubs\Which\Locator $commandLocator The command locator.  This
     *     helps locate commands using PATH.
     * @param string|string[] $pagerPaths The names to the potential pagers.
     *     The first command in the list that can be located will be used.
     * @param array $options {
     *     @type \Habitat\Environment\Environment $environment The environment
     *         variable wrapper.  Defaults to null, which just uses the built-in
     *         getenv.
     * }
     */
    public function __construct(CommandLocator $commandLocator, $pagerPaths = array('sensible-pager', 'less', 'more'), array $options = array())
    {
        $this->_commandLocator = $commandLocator;
        $this->_pagerPaths = array_values((array)$pagerPaths);

        if (isset($options['environment'])) {
            $this->_environment = $options['environment'];
        }
    }

    /**
     * Get the path to the user's preferred pager.
     *
     * @api
     * @return string|null The path to the user's preferred pager if one is
     *     found.
     */
    public function get()
    {
        $pager = $this->_environment ? $this->_environment->getenv('PAGER') : getenv('PAGER');

        return $pager ?: $this->_getDefaultPager();
    }

    /**
     * View the given file using the symfony process builder to build the
     * symfony process to execute.
     *
     * @api
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder The
     *     process builder.
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
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder The
     *     process builder.
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
     * Gets the path to the default pager using the locator if it is set.
     *
     * @return string|null The path to the default pager.
     */
    protected function _getDefaultPager()
    {
        foreach ($this->_pagerPaths as $pagerPath) {
            $location = $this->_commandLocator->locate(basename($pagerPath));
            if ($location !== null) {
                return $location;
            }
        }

        return null;
    }
}

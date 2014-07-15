<?php
namespace Nubs\Sensible;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Wraps the browser to execute it.
 */
class Browser
{
    /** @type string The browser command to use. */
    private $_browserCommand;

    /**
     * Initialize the browser command.
     *
     * @api
     * @param string $browserCommand The browser command to use.
     */
    public function __construct($browserCommand)
    {
        $this->_browserCommand = $browserCommand;
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
        $proc = $processBuilder->setPrefix($this->_browserCommand)->setArguments([$uri])->getProcess();
        $proc->setTty(true)->run();

        return $proc;
    }
}

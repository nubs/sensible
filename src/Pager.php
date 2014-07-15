<?php
namespace Nubs\Sensible;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Wraps the pager to execute it.
 */
class Pager
{
    /** @type string The pager command to use. */
    private $_pagerCommand;

    /**
     * Initialize the pager command.
     *
     * @api
     * @param string $pagerCommand The pager command to use.
     */
    public function __construct($pagerCommand)
    {
        $this->_pagerCommand = $pagerCommand;
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
        $proc = $processBuilder->setPrefix($this->_pagerCommand)->setArguments([$filePath])->getProcess();
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
        $proc = $processBuilder->setPrefix($this->_pagerCommand)->setInput($data)->getProcess();
        $proc->setTty(true)->run();

        return $proc;
    }
}

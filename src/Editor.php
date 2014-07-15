<?php
namespace Nubs\Sensible;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Wraps the editor to execute it.
 */
class Editor
{
    /** @type string The editor command to use. */
    private $_editorCommand;

    /**
     * Initialize the editor command.
     *
     * @api
     * @param string $editorCommand The editor command to use.
     */
    public function __construct($editorCommand)
    {
        $this->_editorCommand = $editorCommand;
    }

    /**
     * Edit the given file using the symfony process builder to build the
     * symfony process to execute.
     *
     * @api
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder The
     *     process builder.
     * @param string $filePath The path to the file to edit.
     * @return \Symfony\Component\Process\Process The already-executed process.
     */
    public function editFile(ProcessBuilder $processBuilder, $filePath)
    {
        $proc = $processBuilder->setPrefix($this->_editorCommand)->setArguments([$filePath])->getProcess();
        $proc->setTty(true)->run();

        return $proc;
    }

    /**
     * Edit the given data using the symfony process builder to build the
     * symfony process to execute.
     *
     * @api
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder The
     *     process builder.
     * @param string $data The data to edit.
     * @return string The edited data (left alone if the editor returns a
     *     failure).
     */
    public function editData(ProcessBuilder $processBuilder, $data)
    {
        $filePath = tempnam(sys_get_temp_dir(), 'sensibleEditor');
        file_put_contents($filePath, $data);

        $proc = $this->editFile($processBuilder, $filePath);
        if ($proc->isSuccessful()) {
            $data = file_get_contents($filePath);
        }

        unlink($filePath);

        return $data;
    }
}

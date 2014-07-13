<?php
namespace Nubs\Sensible;

use Nubs\Which\Locator as CommandLocator;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Provides access to the user's preferred editor command.
 *
 * Editors are found by a configurable list.
 */
class Editor
{
    /** @type string[] The paths to potential editors. */
    protected $_editorPaths;

    /** @type \Habitat\Environment\Environment The environment variable wrapper. */
    protected $_environment;

    /** @type \Nubs\Which\Locator The command locator. */
    protected $_commandLocator;

    /**
     * Initialize the editor loader.
     *
     * @api
     * @param \Nubs\Which\Locator $commandLocator The command locator.  This
     *     helps locate commands using PATH.
     * @param string|string[] $editorPaths The names to the potential editors.
     *     The first command in the list that can be located will be used.
     * @param array $options {
     *     @type \Habitat\Environment\Environment $environment The environment
     *         variable wrapper.  Defaults to null, which just uses the built-in
     *         getenv.
     * }
     */
    public function __construct(CommandLocator $commandLocator, $editorPaths = array('sensible-editor', 'nano', 'vim', 'ed'), array $options = array())
    {
        $this->_commandLocator = $commandLocator;
        $this->_editorPaths = array_values((array)$editorPaths);

        if (isset($options['environment'])) {
            $this->_environment = $options['environment'];
        }
    }

    /**
     * Get the path to the user's preferred editor.
     *
     * @api
     * @return string|null The path to the user's preferred editor if one is
     *     found.
     */
    public function get()
    {
        $editor = $this->_environment ? $this->_environment->getenv('EDITOR') : getenv('EDITOR');

        return $editor ?: $this->_getDefaultEditor();
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
        $proc = $processBuilder->setPrefix($this->get())->setArguments(array($filePath))->getProcess();
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

    /**
     * Gets the path to the default editor using the locator if it is set.
     *
     * @return string|null The path to the default editor.
     */
    protected function _getDefaultEditor()
    {
        foreach ($this->_editorPaths as $editorPath) {
            $location = $this->_commandLocator->locate(basename($editorPath));
            if ($location !== null) {
                return $location;
            }
        }

        return null;
    }
}

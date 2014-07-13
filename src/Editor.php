<?php
namespace Nubs\Sensible;

use Habitat\Environment\Environment;
use Nubs\Which\Locator as CommandLocator;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Provides access to the user's preferred editor command.
 *
 * Editors are found by a configurable list.
 */
class Editor
{
    /** @type string[] The names of potential editors. */
    protected $_editors;

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
     * @param string|string[] $editors The names to the potential editors.
     *     The first command in the list that can be located will be used.
     * @param \Habitat\Environment\Environment $environment The environment
     *     variable wrapper.  Defaults to null, which just uses the built-in
     *     getenv.
     */
    public function __construct(CommandLocator $commandLocator, $editors = ['sensible-editor', 'nano', 'vim', 'ed'], Environment $environment = null)
    {
        $this->_commandLocator = $commandLocator;
        $this->_editors = array_values((array)$editors);
        $this->_environment = $environment;
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
        $proc = $processBuilder->setPrefix($this->get())->setArguments([$filePath])->getProcess();
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
        foreach ($this->_editors as $editor) {
            $location = $this->_commandLocator->locate(basename($editor));
            if ($location !== null) {
                return $location;
            }
        }

        return null;
    }
}

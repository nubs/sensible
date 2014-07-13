<?php
namespace Nubs\Sensible;

use Nubs\Which\Locator as CommandLocator;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Provides access to the user's preferred editor command.
 *
 * If the user has a "sensible-editor" command, that takes preference to other
 * behavior.  If that does not exist, then the "EDITOR" environment variable is
 * used.  If that does not exist, then a default editor is used.
 */
class Editor
{
    /** @type string[] The paths to default editor choices to use if no alternative is found. */
    protected $_defaultEditorPath = array('/usr/bin/sensible-editor', '/usr/bin/nano', '/usr/bin/vim', '/bin/ed');

    /** @type \Habitat\Environment\Environment The environment variable wrapper. */
    protected $_environment;

    /** @type \Nubs\Which\Locator The command locator. */
    protected $_commandLocator;

    /**
     * Initialize the editor loader and configure the options
     *
     * @api
     * @param \Nubs\Which\Locator $commandLocator The command locator.  This
     *     helps locate commands using PATH.
     * @param array $options {
     *     @type string|string[] $defaultEditorPath The paths to the default
     *         editor choices to use if no alternative is found.  The first
     *         editor in the list that can be located will be used.  Defaults to
     *         ['/usr/bin/sensible-editor', '/usr/bin/nano', '/usr/bin/vim',
     *         '/bin/ed'].
     *     @type \Habitat\Environment\Environment $environment The environment
     *         variable wrapper.  Defaults to null, which just uses the built-in
     *         getenv.
     * }
     */
    public function __construct(CommandLocator $commandLocator, array $options = array())
    {
        $this->_commandLocator = $commandLocator;

        if (isset($options['defaultEditorPath'])) {
            $this->_defaultEditorPath = array_values((array)$options['defaultEditorPath']);
        }

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
        foreach ($this->_defaultEditorPath as $editorPath) {
            $location = $this->_commandLocator->locate(basename($editorPath));
            if ($location !== null) {
                return $location;
            }
        }

        return null;
    }
}

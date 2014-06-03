<?php
namespace Nubs\Sensible;

/**
 * Provides access to the user's preferred editor command.
 *
 * If the user has a "sensible-editor" command, that takes preference to other
 * behavior.  If that does not exist, then the "EDITOR" environment variable is
 * used.  If that does not exist, then a default editor is used (/bin/ed by
 * default).
 */
class Editor
{
    /** @type string The path to debian's sensible-editor. */
    protected $_sensibleEditorPath;

    /** @type string The path to the default editor to use if no alternative is found. */
    protected $_defaultEditorPath;

    /** @type \Habitat\Environment\Environment The environment variable wrapper. */
    protected $_environment;

    /**
     * Initialize the editor loader and configure the options
     *
     * @api
     * @param array $options {
     *     @type string $sensibleEditorPath The path to debian's
     *         sensible-editor.  Defaults to '/usr/bin/sensible-editor'.
     *     @type string $defaultEditorPath The path to the default editor to use
     *         if no alternative is found.  Defaults to
     *         '/usr/bin/sensible-editor'.
     *     @type \Habitat\Environment\Environment The environment variable
     *         wrapper.  Defaults to null, which just uses the built-in getenv.
     * }
     */
    public function __construct(array $options = array())
    {
        $this->_sensibleEditorPath = isset($options['sensibleEditorPath']) ? $options['sensibleEditorPath'] : '/usr/bin/sensible-editor';
        $this->_defaultEditorPath = isset($options['defaultEditorPath']) ? $options['defaultEditorPath'] : '/bin/ed';

        if (isset($options['environment'])) {
            $this->_environment = $options['environment'];
        }
    }

    /**
     * Get the path to the user's preferred editor.
     *
     * @api
     * @return string The path to the user's preferred editor.
     */
    public function get()
    {
        if (is_executable($this->_sensibleEditorPath)) {
            return $this->_sensibleEditorPath;
        }

        $editor = $this->_environment ? $this->_environment->getenv('EDITOR') : getenv('EDITOR');

        return $editor ?: $this->_defaultEditorPath;
    }
}

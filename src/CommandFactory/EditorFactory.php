<?php
namespace Nubs\Sensible\CommandFactory;

use Exception;
use Habitat\Environment\Environment;
use Nubs\Sensible\Editor;
use Nubs\Sensible\Strategy\EditorStrategy;
use Nubs\Which\Locator as CommandLocator;

/**
 * Uses the EditorStrategy to locate an editor.
 */
class EditorFactory
{
    /** @type \Nubs\Sensible\Strategy\PagerFactory The pager strategy. */
    private $_strategy;

    /**
     * Create the editor.
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
        $this->_strategy = new EditorStrategy($editors, $commandLocator, $environment);
    }

    /**
     * Create the editor object using the strategy.
     *
     * @api
     * @return \Nubs\Sensible\Editor The created editor object.
     * @throws \Exception if no editor can be found.
     */
    public function create()
    {
        $editor = $this->_strategy->get();
        if ($editor === null) {
            throw new Exception('Failed to locate a sensible command');
        }

        return new Editor($editor);
    }
}

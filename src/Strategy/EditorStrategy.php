<?php
namespace Nubs\Sensible\Strategy;

use Habitat\Environment\Environment;
use Nubs\Which\Locator as CommandLocator;

/**
 * Uses the EnvironmentVariableStrategy (EDITOR) and CommandLocatorStrategy to
 * create an Editor.
 */
class EditorStrategy extends ListStrategy implements StrategyInterface
{
    /**
     * Initialize the editor strategy.
     *
     * @param string|string[] $editors The names to the potential editors.
     *     The first command in the list that can be located will be used.
     * @param \Nubs\Which\Locator $commandLocator The command locator.  This
     *     helps locate commands using PATH.
     * @param \Habitat\Environment\Environment $environment The environment
     *     variable wrapper.  Defaults to null, which just uses the built-in
     *     getenv.
     */
    public function __construct($editors, CommandLocator $commandLocator, Environment $environment = null)
    {
        parent::__construct([new EnvironmentVariableStrategy('EDITOR', $environment), new CommandLocatorStrategy($editors, $commandLocator)]);
    }
}

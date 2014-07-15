<?php
namespace Nubs\Sensible\CommandFactory;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\CommandFactory\EditorFactory
 * @uses \Nubs\Sensible\Strategy\EditorStrategy
 * @uses \Nubs\Sensible\Strategy\CommandLocatorStrategy
 * @uses \Nubs\Sensible\Strategy\EnvironmentVariableStrategy
 * @uses \Nubs\Sensible\Strategy\ListStrategy
 * @uses \Nubs\Sensible\Editor::__construct
 */
class EditorFactoryTest extends PHPUnit_Framework_TestCase
{
    private $_environment;
    private $_commandLocator;

    public function setUp()
    {
        $this->_environment = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(['getenv'])->getMock();
        $this->_commandLocator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(['locate'])->getMock();
    }

    /**
     * Verify that create works with a valid editor.
     *
     * @test
     * @covers ::__construct
     * @covers ::create
     */
    public function create()
    {
        $this->_environment->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue('/foo/env'));

        $factory = new EditorFactory($this->_commandLocator, ['a', 'b', 'c'], $this->_environment);

        $editor = $factory->create();
    }

    /**
     * Verify that create throws an exception when no command found.
     *
     * @test
     * @covers ::__construct
     * @covers ::create
     * @expectedException \Exception
     * @expectedExceptionMessage Failed to locate a sensible command
     */
    public function createNoCommandFound()
    {
        $this->_environment->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->once())->method('locate')->with('a')->will($this->returnValue(null));

        $factory = new EditorFactory($this->_commandLocator, ['a'], $this->_environment);

        $editor = $factory->create();
    }
}

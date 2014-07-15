<?php
namespace Nubs\Sensible\Strategy;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Strategy\EditorStrategy
 */
class EditorStrategyTest extends PHPUnit_Framework_TestCase
{
    private $_environment;
    private $_commandLocator;

    public function setUp()
    {
        $this->_environment = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(['getenv'])->getMock();
        $this->_commandLocator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(['locate'])->getMock();
    }

    /**
     * Verify that the basic behavior works from environment variable.
     *
     * @test
     * @covers ::__construct
     * @uses \Nubs\Sensible\Strategy\ListStrategy
     * @uses \Nubs\Sensible\Strategy\EnvironmentVariableStrategy
     * @uses \Nubs\Sensible\Strategy\CommandLocatorStrategy::__construct
     * @covers ::get
     */
    public function getFromEnvironmentVariable()
    {
        $this->_environment->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue('/foo/env'));

        $strategy = new EditorStrategy(['a', 'b', 'c'], $this->_commandLocator, $this->_environment);
        $this->assertSame('/foo/env', $strategy->get());
    }

    /**
     * Verify that the basic behavior works from locator.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @uses \Nubs\Sensible\Strategy\ListStrategy
     * @uses \Nubs\Sensible\Strategy\EnvironmentVariableStrategy
     * @uses \Nubs\Sensible\Strategy\CommandLocatorStrategy
     */
    public function getFromLocator()
    {
        $this->_environment->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue(false));
        $this->_commandLocator->expects($this->at(0))->method('locate')->with('a')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(1))->method('locate')->with('b')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(2))->method('locate')->with('c')->will($this->returnValue('/foo/c'));

        $strategy = new EditorStrategy(['a', 'b', 'c'], $this->_commandLocator, $this->_environment);
        $this->assertSame('/foo/c', $strategy->get());
    }

    /**
     * Verify that the get command returns null when none found.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @uses \Nubs\Sensible\Strategy\ListStrategy
     * @uses \Nubs\Sensible\Strategy\EnvironmentVariableStrategy
     * @uses \Nubs\Sensible\Strategy\CommandLocatorStrategy
     */
    public function getNoneFound()
    {
        $this->_environment->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue(false));
        $this->_commandLocator->expects($this->at(0))->method('locate')->with('a')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(1))->method('locate')->with('b')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(2))->method('locate')->with('c')->will($this->returnValue(null));

        $strategy = new EditorStrategy(['a', 'b', 'c'], $this->_commandLocator, $this->_environment);
        $this->assertNull($strategy->get());
    }
}

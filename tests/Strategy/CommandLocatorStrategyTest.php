<?php
namespace Nubs\Sensible\Strategy;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Strategy\CommandLocatorStrategy
 */
class CommandLocatorStrategyTest extends PHPUnit_Framework_TestCase
{
    private $_commandLocator;

    public function setUp()
    {
        $this->_commandLocator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(['locate'])->getMock();
    }

    /**
     * Verify that the basic behavior works.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function get()
    {
        $this->_commandLocator->expects($this->at(0))->method('locate')->with('a')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(1))->method('locate')->with('b')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(2))->method('locate')->with('c')->will($this->returnValue('/foo/c'));

        $strategy = new CommandLocatorStrategy(['a', 'b', 'c'], $this->_commandLocator);
        $this->assertSame('/foo/c', $strategy->get());
    }

    /**
     * Verify that the get command returns null when none found.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function getNoneFound()
    {
        $this->_commandLocator->expects($this->at(0))->method('locate')->with('a')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(1))->method('locate')->with('b')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(2))->method('locate')->with('c')->will($this->returnValue(null));

        $strategy = new CommandLocatorStrategy(['a', 'b', 'c'], $this->_commandLocator);
        $this->assertNull($strategy->get());
    }
}

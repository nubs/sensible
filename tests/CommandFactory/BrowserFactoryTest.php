<?php
namespace Nubs\Sensible\CommandFactory;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\CommandFactory\BrowserFactory
 * @uses \Nubs\Sensible\Strategy\BrowserStrategy
 * @uses \Nubs\Sensible\Strategy\CommandLocatorStrategy
 * @uses \Nubs\Sensible\Browser::__construct
 */
class BrowserFactoryTest extends PHPUnit_Framework_TestCase
{
    private $_commandLocator;

    public function setUp()
    {
        $this->_commandLocator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(['locate'])->getMock();
    }

    /**
     * Verify that create works with a valid browser.
     *
     * @test
     * @covers ::__construct
     * @covers ::create
     */
    public function create()
    {
        $this->_commandLocator->expects($this->at(0))->method('locate')->with('a')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(1))->method('locate')->with('b')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(2))->method('locate')->with('c')->will($this->returnValue('/foo/c'));

        $factory = new BrowserFactory($this->_commandLocator, ['a', 'b', 'c']);

        $browser = $factory->create();
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
        $this->_commandLocator->expects($this->once())->method('locate')->with('a')->will($this->returnValue(null));

        $factory = new BrowserFactory($this->_commandLocator, ['a']);

        $browser = $factory->create();
    }
}

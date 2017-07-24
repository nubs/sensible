<?php
namespace Nubs\Sensible\CommandFactory;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\CommandFactory\PagerFactory
 * @uses \Nubs\Sensible\Strategy\PagerStrategy
 * @uses \Nubs\Sensible\Strategy\CommandLocatorStrategy
 * @uses \Nubs\Sensible\Strategy\EnvironmentVariableStrategy
 * @uses \Nubs\Sensible\Strategy\ListStrategy
 * @uses \Nubs\Sensible\Pager::__construct
 */
class PagerFactoryTest extends TestCase
{
    private $_environment;
    private $_commandLocator;

    public function setUp()
    {
        $this->_environment = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(['getenv'])->getMock();
        $this->_commandLocator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(['locate'])->getMock();
    }

    /**
     * Verify that create works with a valid pager.
     *
     * @test
     * @covers ::__construct
     * @covers ::create
     * @covers \Nubs\Sensible\CommandFactory\CommandFactoryTrait
     */
    public function create()
    {
        $this->_environment->expects($this->once())->method('getenv')->with('PAGER')->will($this->returnValue('/foo/env'));

        $factory = new PagerFactory($this->_commandLocator, ['a', 'b', 'c'], $this->_environment);

        $pager = $factory->create();
    }

    /**
     * Verify that create throws an exception when no command found.
     *
     * @test
     * @covers ::__construct
     * @covers ::create
     * @covers \Nubs\Sensible\CommandFactory\CommandFactoryTrait
     * @expectedException \Exception
     * @expectedExceptionMessage Failed to locate a sensible command
     */
    public function createNoCommandFound()
    {
        $this->_environment->expects($this->once())->method('getenv')->with('PAGER')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->once())->method('locate')->with('a')->will($this->returnValue(null));

        $factory = new PagerFactory($this->_commandLocator, ['a'], $this->_environment);

        $pager = $factory->create();
    }
}

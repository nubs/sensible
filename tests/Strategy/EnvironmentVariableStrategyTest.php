<?php
namespace Nubs\Sensible\Strategy;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Strategy\EnvironmentVariableStrategy
 */
class EnvironmentVariableStrategyTest extends TestCase
{
    private $_environment;

    public function setUp()
    {
        $this->_environment = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(['getenv'])->getMock();
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
        $this->_environment->expects($this->once())->method('getenv')->with('A')->will($this->returnValue('foo'));

        $strategy = new EnvironmentVariableStrategy('A', $this->_environment);
        $this->assertSame('foo', $strategy->get());
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
        $this->_environment->expects($this->once())->method('getenv')->with('A')->will($this->returnValue(false));

        $strategy = new EnvironmentVariableStrategy('A', $this->_environment);
        $this->assertNull($strategy->get());
    }
}

<?php
namespace Nubs\Sensible\Strategy;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Strategy\ListStrategy
 */
class ListStrategyTest extends TestCase
{
    /**
     * Verify that the basic behavior works.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function get()
    {
        $nullStrategy = $this->getMockBuilder('\Nubs\Sensible\Strategy\StrategyInterface')->setMethods(['get'])->getMock();
        $nullStrategy->expects($this->once())->method('get')->will($this->returnValue(null));

        $fooStrategy = $this->getMockBuilder('\Nubs\Sensible\Strategy\StrategyInterface')->setMethods(['get'])->getMock();
        $fooStrategy->expects($this->once())->method('get')->will($this->returnValue('foo'));

        $strategy = new ListStrategy([$nullStrategy, $fooStrategy]);
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
        $nullStrategy = $this->getMockBuilder('\Nubs\Sensible\Strategy\StrategyInterface')->setMethods(['get'])->getMock();
        $nullStrategy->expects($this->exactly(2))->method('get')->will($this->returnValue(null));

        $strategy = new ListStrategy([$nullStrategy, $nullStrategy]);
        $this->assertNull($strategy->get());
    }
}

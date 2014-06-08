<?php
namespace Nubs\Sensible;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Pager
 */
class PagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Verify that the "sensible-pager" gets used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function getSensiblePager()
    {
        $pager = new Pager(array('sensiblePagerPath' => __DIR__));

        $this->assertSame(__DIR__, $pager->get());
    }

    /**
     * Verify that the "PAGER" environment variable gets used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function getEnvironmentVariablePager()
    {
        $env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(array('getenv'))->getMock();
        $env->expects($this->once())->method('getenv')->with('PAGER')->will($this->returnValue('foo'));
        $pager = new Pager(array('sensiblePagerPath' => 'nonexistant', 'environment' => $env));

        $this->assertSame('foo', $pager->get());
    }

    /**
     * Verify that the default pager is used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function getDefaultPager()
    {
        $env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(array('getenv'))->getMock();
        $env->expects($this->once())->method('getenv')->with('PAGER')->will($this->returnValue(null));
        $pager = new Pager(array('sensiblePagerPath' => 'nonexistant', 'environment' => $env, 'defaultPagerPath' => 'bar'));

        $this->assertSame('bar', $pager->get());
    }

    /**
     * Verify that viewFile works.
     *
     * @test
     * @covers ::viewFile
     * @uses \Nubs\Sensible\Pager::__construct
     * @uses \Nubs\Sensible\Pager::get
     */
    public function viewFile()
    {
        $pagerPath = '/the/pager';
        $filePath = '/the/file';
        $pager = $this->getMockBuilder('\Nubs\Sensible\Pager')->setMethods(array('get'))->getMock();
        $pager->expects($this->once())->method('get')->will($this->returnValue($pagerPath));

        $process = $this->getMockBuilder('\Symfony\Component\Process\Process')->setMethods(array('setTty', 'run'))->getMock();
        $process->expects($this->once())->method('setTty')->with(true)->will($this->returnSelf());
        $process->expects($this->once())->method('run')->will($this->returnValue(0));

        $processBuilder = $this->getMockBuilder('\Symfony\Component\Process\ProcessBuilder')->setMethods(array('setPrefix', 'setArguments', 'getProcess'))->getMock();
        $processBuilder->expects($this->once())->method('setPrefix')->with($pagerPath)->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('setArguments')->with(array($filePath))->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($process));

        $this->assertSame($process, $pager->viewFile($processBuilder, $filePath));
    }

    /**
     * Verify that viewFile works.
     *
     * @test
     * @covers ::viewData
     * @uses \Nubs\Sensible\Pager::__construct
     * @uses \Nubs\Sensible\Pager::get
     */
    public function viewData()
    {
        $pagerPath = '/the/pager';
        $data = 'foo bar';
        $pager = $this->getMockBuilder('\Nubs\Sensible\Pager')->setMethods(array('get'))->getMock();
        $pager->expects($this->once())->method('get')->will($this->returnValue($pagerPath));

        $process = $this->getMockBuilder('\Symfony\Component\Process\Process')->setMethods(array('setTty', 'run'))->getMock();
        $process->expects($this->once())->method('setTty')->with(true)->will($this->returnSelf());
        $process->expects($this->once())->method('run')->will($this->returnValue(0));

        $processBuilder = $this->getMockBuilder('\Symfony\Component\Process\ProcessBuilder')->setMethods(array('setPrefix', 'setInput', 'getProcess'))->getMock();
        $processBuilder->expects($this->once())->method('setPrefix')->with($pagerPath)->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('setInput')->with($data)->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($process));

        $this->assertSame($process, $pager->viewData($processBuilder, $data));
    }
}

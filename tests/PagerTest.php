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
     * @covers ::_getSensiblePager
     */
    public function getSensiblePager()
    {
        $pager = new Pager(array('sensiblePagerPath' => __DIR__));

        $this->assertSame(__DIR__, $pager->get());
    }

    /**
     * Verify that the "sensible-pager" gets used and located properly.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getSensiblePager
     */
    public function getSensiblePagerWithLocator()
    {
        $locator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(array('locate'))->getMock();
        $locator->expects($this->once())->method('locate')->with('sensible-pager')->will($this->returnValue('/foo/bar/sensible-pager'));
        $pager = new Pager(array('commandLocator' => $locator));

        $this->assertSame('/foo/bar/sensible-pager', $pager->get());
    }

    /**
     * Verify that the "PAGER" environment variable gets used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getSensiblePager
     */
    public function getEnvironmentVariablePager()
    {
        $env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(array('getenv'))->getMock();
        $env->expects($this->once())->method('getenv')->with('PAGER')->will($this->returnValue('foo'));

        $locator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(array('locate'))->getMock();
        $locator->expects($this->once())->method('locate')->with('sensible-pager')->will($this->returnValue(null));

        $pager = new Pager(array('environment' => $env, 'commandLocator' => $locator));

        $this->assertSame('foo', $pager->get());
    }

    /**
     * Verify that the default pager is used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getSensiblePager
     * @covers ::_getDefaultPager
     */
    public function getDefaultPager()
    {
        $env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(array('getenv'))->getMock();
        $env->expects($this->once())->method('getenv')->with('PAGER')->will($this->returnValue(null));
        $pager = new Pager(array('sensiblePagerPath' => 'nonexistant', 'environment' => $env, 'defaultPagerPath' => 'bar'));

        $this->assertSame('bar', $pager->get());
    }

    /**
     * Verify that the default pager is used and located properly.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getSensiblePager
     * @covers ::_getDefaultPager
     */
    public function getDefaultPagerWithLocator()
    {
        $env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(array('getenv'))->getMock();
        $env->expects($this->once())->method('getenv')->with('PAGER')->will($this->returnValue(null));

        $locator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(array('locate'))->getMock();
        $locator->expects($this->at(0))->method('locate')->with('sensible-pager')->will($this->returnValue(null));
        $locator->expects($this->at(1))->method('locate')->with('more')->will($this->returnValue('/foo/bar/more'));

        $pager = new Pager(array('environment' => $env, 'commandLocator' => $locator));

        $this->assertSame('/foo/bar/more', $pager->get());
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

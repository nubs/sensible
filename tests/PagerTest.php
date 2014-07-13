<?php
namespace Nubs\Sensible;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Pager
 */
class PagerTest extends PHPUnit_Framework_TestCase
{
    private $_commandLocator;
    private $_env;

    public function setUp()
    {
        $this->_commandLocator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(['locate'])->getMock();
        $this->_env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(['getenv'])->getMock();
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
        $this->_env->expects($this->once())->method('getenv')->with('PAGER')->will($this->returnValue('foo'));

        $pager = new Pager($this->_commandLocator, [], $this->_env);

        $this->assertSame('foo', $pager->get());
    }

    /**
     * Verify that the default pager is used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getDefaultPager
     */
    public function getDefaultPager()
    {
        $this->_env->expects($this->once())->method('getenv')->with('PAGER')->will($this->returnValue(null));

        $this->_commandLocator->expects($this->at(0))->method('locate')->with('bar')->will($this->returnValue('/foo/bar'));

        $pager = new Pager($this->_commandLocator, 'bar', $this->_env);

        $this->assertSame('/foo/bar', $pager->get());
    }

    /**
     * Verify that the default pager is used and located properly.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getDefaultPager
     */
    public function getDefaultPagerWithLocator()
    {
        $this->_env->expects($this->once())->method('getenv')->with('PAGER')->will($this->returnValue(null));

        $this->_commandLocator->expects($this->at(0))->method('locate')->with('a')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(1))->method('locate')->with('b')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(2))->method('locate')->with('c')->will($this->returnValue('/foo/bar/c'));

        $pager = new Pager($this->_commandLocator, ['a', 'b', 'c'], $this->_env);

        $this->assertSame('/foo/bar/c', $pager->get());
    }

    /**
     * Verify that a pager is returned even when none can be located.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getDefaultPager
     */
    public function getDefaultPagerWhenNoneLocated()
    {
        $this->_env->expects($this->once())->method('getenv')->with('PAGER')->will($this->returnValue(null));

        $this->_commandLocator->expects($this->at(0))->method('locate')->with('a')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(1))->method('locate')->with('b')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(2))->method('locate')->with('c')->will($this->returnValue(null));

        $pager = new Pager($this->_commandLocator, ['a', 'b', 'c'], $this->_env);

        $this->assertNull($pager->get());
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
        $pager = $this->getMockBuilder('\Nubs\Sensible\Pager')->disableOriginalConstructor()->setMethods(['get'])->getMock();
        $pager->expects($this->once())->method('get')->will($this->returnValue($pagerPath));

        $process = $this->getMockBuilder('\Symfony\Component\Process\Process')->disableOriginalConstructor()->setMethods(['setTty', 'run'])->getMock();
        $process->expects($this->once())->method('setTty')->with(true)->will($this->returnSelf());
        $process->expects($this->once())->method('run')->will($this->returnValue(0));

        $processBuilder = $this->getMockBuilder('\Symfony\Component\Process\ProcessBuilder')->disableOriginalConstructor()->setMethods(['setPrefix', 'setArguments', 'getProcess'])->getMock();
        $processBuilder->expects($this->once())->method('setPrefix')->with($pagerPath)->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('setArguments')->with([$filePath])->will($this->returnSelf());
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
        $pager = $this->getMockBuilder('\Nubs\Sensible\Pager')->disableOriginalConstructor()->setMethods(['get'])->getMock();
        $pager->expects($this->once())->method('get')->will($this->returnValue($pagerPath));

        $process = $this->getMockBuilder('\Symfony\Component\Process\Process')->disableOriginalConstructor()->setMethods(['setTty', 'run'])->getMock();
        $process->expects($this->once())->method('setTty')->with(true)->will($this->returnSelf());
        $process->expects($this->once())->method('run')->will($this->returnValue(0));

        $processBuilder = $this->getMockBuilder('\Symfony\Component\Process\ProcessBuilder')->disableOriginalConstructor()->setMethods(['setPrefix', 'setInput', 'getProcess'])->getMock();
        $processBuilder->expects($this->once())->method('setPrefix')->with($pagerPath)->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('setInput')->with($data)->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($process));

        $this->assertSame($process, $pager->viewData($processBuilder, $data));
    }
}

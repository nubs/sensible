<?php
namespace Nubs\Sensible;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Pager
 */
class PagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Verify that viewFile works.
     *
     * @test
     * @covers ::__construct
     * @covers ::viewFile
     */
    public function viewFile()
    {
        $pagerPath = '/the/pager';
        $filePath = '/the/file';

        $pager = new Pager($pagerPath);

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
     * @covers ::__construct
     * @covers ::viewData
     */
    public function viewData()
    {
        $pagerPath = '/the/pager';
        $data = 'foo bar';

        $pager = new Pager($pagerPath);

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

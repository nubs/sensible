<?php
namespace Nubs\Sensible;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Browser
 */
class BrowserTest extends TestCase
{
    /**
     * Verify that viewURI works.
     *
     * @test
     * @covers ::__construct
     * @covers ::viewURI
     */
    public function viewURI()
    {
        $browserPath = '/the/browser';
        $uri = 'http://the.uri';

        $browser = new Browser($browserPath);

        $process = $this->getMockBuilder('\Symfony\Component\Process\Process')->disableOriginalConstructor()->setMethods(['setTty', 'run'])->getMock();
        $process->expects($this->once())->method('setTty')->with(true)->will($this->returnSelf());
        $process->expects($this->once())->method('run')->will($this->returnValue(0));

        $processBuilder = $this->getMockBuilder('\Symfony\Component\Process\ProcessBuilder')->disableOriginalConstructor()->setMethods(['setPrefix', 'setArguments', 'getProcess'])->getMock();
        $processBuilder->expects($this->once())->method('setPrefix')->with($browserPath)->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('setArguments')->with([$uri])->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($process));

        $this->assertSame($process, $browser->viewURI($processBuilder, $uri));
    }
}

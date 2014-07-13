<?php
namespace Nubs\Sensible;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Browser
 */
class BrowserTest extends PHPUnit_Framework_TestCase
{
    private $_commandLocator;

    public function setUp()
    {
        $this->_commandLocator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(['locate'])->getMock();
    }

    /**
     * Verify that the default browser is used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function getDefaultBrowser()
    {
        $this->_commandLocator->expects($this->at(0))->method('locate')->with('bar')->will($this->returnValue('/foo/bar'));
        $browser = new Browser($this->_commandLocator, 'bar');

        $this->assertSame('/foo/bar', $browser->get());
    }

    /**
     * Verify that the default browser is used and located properly.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function getDefaultBrowserWithLocator()
    {
        $this->_commandLocator->expects($this->at(0))->method('locate')->with('a')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(1))->method('locate')->with('b')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(2))->method('locate')->with('c')->will($this->returnValue('/foo/bar/c'));

        $browser = new Browser($this->_commandLocator, ['a', 'b', 'c']);

        $this->assertSame('/foo/bar/c', $browser->get());
    }

    /**
     * Verify that a browser is returned even when none can be located.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function getDefaultBrowserWhenNoneLocated()
    {
        $this->_commandLocator->expects($this->at(0))->method('locate')->with('a')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(1))->method('locate')->with('b')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(2))->method('locate')->with('c')->will($this->returnValue(null));

        $browser = new Browser($this->_commandLocator, ['a', 'b', 'c']);

        $this->assertNull($browser->get());
    }

    /**
     * Verify that viewURI works.
     *
     * @test
     * @covers ::viewURI
     * @uses \Nubs\Sensible\Browser::__construct
     * @uses \Nubs\Sensible\Browser::get
     */
    public function viewURI()
    {
        $browserPath = '/the/browser';
        $uri = 'http://the.uri';
        $browser = $this->getMockBuilder('\Nubs\Sensible\Browser')->disableOriginalConstructor()->setMethods(['get'])->getMock();
        $browser->expects($this->once())->method('get')->will($this->returnValue($browserPath));

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

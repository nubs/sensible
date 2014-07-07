<?php
namespace Nubs\Sensible;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Browser
 */
class BrowserTest extends PHPUnit_Framework_TestCase
{
    /**
     * Verify that the "sensible-browser" gets used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getSensibleBrowser
     */
    public function getSensibleBrowser()
    {
        $browser = new Browser(array('sensibleBrowserPath' => __DIR__));

        $this->assertSame(__DIR__, $browser->get());
    }

    /**
     * Verify that the "sensible-browser" gets used and located properly.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getSensibleBrowser
     */
    public function getSensibleBrowserWithLocator()
    {
        $locator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(array('locate'))->getMock();
        $locator->expects($this->once())->method('locate')->with('sensible-browser')->will($this->returnValue('/foo/bar/sensible-browser'));
        $browser = new Browser(array('commandLocator' => $locator));

        $this->assertSame('/foo/bar/sensible-browser', $browser->get());
    }

    /**
     * Verify that the default browser is used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getSensibleBrowser
     * @covers ::_getDefaultBrowser
     */
    public function getDefaultBrowser()
    {
        $browser = new Browser(array('sensibleBrowserPath' => 'nonexistant', 'defaultBrowserPath' => 'bar'));

        $this->assertSame('bar', $browser->get());
    }

    /**
     * Verify that the default browser is used and located properly.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getSensibleBrowser
     * @covers ::_getDefaultBrowser
     */
    public function getDefaultBrowserWithLocator()
    {
        $locator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(array('locate'))->getMock();
        $locator->expects($this->at(0))->method('locate')->with('sensible-browser')->will($this->returnValue(null));
        $locator->expects($this->at(1))->method('locate')->with('firefox')->will($this->returnValue(null));
        $locator->expects($this->at(2))->method('locate')->with('chromium-browser')->will($this->returnValue(null));
        $locator->expects($this->at(3))->method('locate')->with('chrome')->will($this->returnValue(null));
        $locator->expects($this->at(4))->method('locate')->with('elinks')->will($this->returnValue('/foo/bar/elinks'));

        $browser = new Browser(array('commandLocator' => $locator));

        $this->assertSame('/foo/bar/elinks', $browser->get());
    }

    /**
     * Verify that a browser is returned even when none can be located.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getSensibleBrowser
     * @covers ::_getDefaultBrowser
     */
    public function getDefaultBrowserWhenNoneLocated()
    {
        $locator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(array('locate'))->getMock();
        $locator->expects($this->at(0))->method('locate')->with('sensible-browser')->will($this->returnValue(null));
        $locator->expects($this->at(1))->method('locate')->with('firefox')->will($this->returnValue(null));
        $locator->expects($this->at(2))->method('locate')->with('chromium-browser')->will($this->returnValue(null));
        $locator->expects($this->at(3))->method('locate')->with('chrome')->will($this->returnValue(null));
        $locator->expects($this->at(4))->method('locate')->with('elinks')->will($this->returnValue(null));

        $browser = new Browser(array('commandLocator' => $locator));

        $this->assertSame('/usr/bin/elinks', $browser->get());
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
        $browser = $this->getMockBuilder('\Nubs\Sensible\Browser')->setMethods(array('get'))->getMock();
        $browser->expects($this->once())->method('get')->will($this->returnValue($browserPath));

        $process = $this->getMockBuilder('\Symfony\Component\Process\Process')->disableOriginalConstructor()->setMethods(array('setTty', 'run'))->getMock();
        $process->expects($this->once())->method('setTty')->with(true)->will($this->returnSelf());
        $process->expects($this->once())->method('run')->will($this->returnValue(0));

        $processBuilder = $this->getMockBuilder('\Symfony\Component\Process\ProcessBuilder')->disableOriginalConstructor()->setMethods(array('setPrefix', 'setArguments', 'getProcess'))->getMock();
        $processBuilder->expects($this->once())->method('setPrefix')->with($browserPath)->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('setArguments')->with(array($uri))->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($process));

        $this->assertSame($process, $browser->viewURI($processBuilder, $uri));
    }
}

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
}

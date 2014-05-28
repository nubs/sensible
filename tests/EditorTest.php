<?php
namespace Nubs\Sensible;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Editor
 */
class EditorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Verify that the "sensible-editor" gets used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function getSensibleEditor()
    {
        $editor = new Editor(array('sensibleEditorPath' => __DIR__));

        $this->assertSame(__DIR__, $editor->get());
    }

    /**
     * Verify that the "EDITOR" environment variable gets used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function getEnvironmentVariableEditor()
    {
        $env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(array('getenv'))->getMock();
        $env->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue('foo'));
        $editor = new Editor(array('sensibleEditorPath' => 'nonexistant', 'environment' => $env));

        $this->assertSame('foo', $editor->get());
    }

    /**
     * Verify that the default editor is used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     */
    public function getDefaultEditor()
    {
        $env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(array('getenv'))->getMock();
        $env->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue(null));
        $editor = new Editor(array('sensibleEditorPath' => 'nonexistant', 'environment' => $env, 'defaultEditorPath' => 'bar'));

        $this->assertSame('bar', $editor->get());
    }
}

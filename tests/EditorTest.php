<?php
namespace Nubs\Sensible;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Editor
 */
class EditorTest extends PHPUnit_Framework_TestCase
{
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

        $editor = new Editor(array('environment' => $env));

        $this->assertSame('foo', $editor->get());
    }

    /**
     * Verify that the default editor is used.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getDefaultEditor
     */
    public function getDefaultEditor()
    {
        $env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(array('getenv'))->getMock();
        $env->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue(null));
        $editor = new Editor(array('environment' => $env, 'defaultEditorPath' => 'bar'));

        $this->assertSame('bar', $editor->get());
    }

    /**
     * Verify that the default editor is used and located properly.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getDefaultEditor
     */
    public function getDefaultEditorWithLocator()
    {
        $env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(array('getenv'))->getMock();
        $env->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue(null));

        $locator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(array('locate'))->getMock();
        $locator->expects($this->at(0))->method('locate')->with('sensible-editor')->will($this->returnValue(null));
        $locator->expects($this->at(1))->method('locate')->with('nano')->will($this->returnValue(null));
        $locator->expects($this->at(2))->method('locate')->with('vim')->will($this->returnValue('/foo/bar/vim'));

        $editor = new Editor(array('environment' => $env, 'commandLocator' => $locator));

        $this->assertSame('/foo/bar/vim', $editor->get());
    }

    /**
     * Verify that an editor is returned even when none can be located.
     *
     * @test
     * @covers ::__construct
     * @covers ::get
     * @covers ::_getDefaultEditor
     */
    public function getDefaultEditorWhenNoneLocated()
    {
        $env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(array('getenv'))->getMock();
        $env->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue(null));

        $locator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(array('locate'))->getMock();
        $locator->expects($this->at(0))->method('locate')->with('sensible-editor')->will($this->returnValue(null));
        $locator->expects($this->at(1))->method('locate')->with('nano')->will($this->returnValue(null));
        $locator->expects($this->at(2))->method('locate')->with('vim')->will($this->returnValue(null));
        $locator->expects($this->at(3))->method('locate')->with('ed')->will($this->returnValue(null));

        $editor = new Editor(array('environment' => $env, 'commandLocator' => $locator));

        $this->assertSame('/bin/ed', $editor->get());
    }

    /**
     * Verify that editFile works.
     *
     * @test
     * @covers ::editFile
     * @uses \Nubs\Sensible\Editor::__construct
     * @uses \Nubs\Sensible\Editor::get
     */
    public function editFile()
    {
        $editorPath = '/the/editor';
        $filePath = '/the/file';
        $editor = $this->getMockBuilder('\Nubs\Sensible\Editor')->setMethods(array('get'))->getMock();
        $editor->expects($this->once())->method('get')->will($this->returnValue($editorPath));

        $process = $this->getMockBuilder('\Symfony\Component\Process\Process')->disableOriginalConstructor()->setMethods(array('setTty', 'run'))->getMock();
        $process->expects($this->once())->method('setTty')->with(true)->will($this->returnSelf());
        $process->expects($this->once())->method('run')->will($this->returnValue(0));

        $processBuilder = $this->getMockBuilder('\Symfony\Component\Process\ProcessBuilder')->disableOriginalConstructor()->setMethods(array('setPrefix', 'setArguments', 'getProcess'))->getMock();
        $processBuilder->expects($this->once())->method('setPrefix')->with($editorPath)->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('setArguments')->with(array($filePath))->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($process));

        $this->assertSame($process, $editor->editFile($processBuilder, $filePath));
    }

    /**
     * Verify that editFile works.
     *
     * @test
     * @covers ::editData
     * @uses \Nubs\Sensible\Editor::__construct
     * @uses \Nubs\Sensible\Editor::get
     * @uses \Nubs\Sensible\Editor::editFile
     */
    public function editData()
    {
        $data = 'foo bar';

        $process = $this->getMockBuilder('\Symfony\Component\Process\Process')->disableOriginalConstructor()->setMethods(array('isSuccessful'))->getMock();
        $process->expects($this->once())->method('isSuccessful')->will($this->returnValue(true));

        $processBuilder = $this->getMockBuilder('\Symfony\Component\Process\ProcessBuilder')->disableOriginalConstructor()->setMethods(array())->getMock();

        $editor = $this->getMockBuilder('\Nubs\Sensible\Editor')->setMethods(array('editFile'))->getMock();
        $editor->expects($this->once())->method('editFile')->will($this->returnValue($process));

        $this->assertSame($data, $editor->editData($processBuilder, $data));
    }
}

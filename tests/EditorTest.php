<?php
namespace Nubs\Sensible;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Editor
 */
class EditorTest extends PHPUnit_Framework_TestCase
{
    private $_commandLocator;
    private $_env;

    public function setUp()
    {
        $this->_commandLocator = $this->getMockBuilder('\Nubs\Which\Locator')->disableOriginalConstructor()->setMethods(['locate'])->getMock();
        $this->_env = $this->getMockBuilder('\Habitat\Environment\Environment')->disableOriginalConstructor()->setMethods(['getenv'])->getMock();
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
        $this->_env->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue('foo'));

        $editor = new Editor($this->_commandLocator, [], $this->_env);

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
        $this->_env->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(0))->method('locate')->with('bar')->will($this->returnValue('/foo/bar'));

        $editor = new Editor($this->_commandLocator, 'bar', $this->_env);

        $this->assertSame('/foo/bar', $editor->get());
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
        $this->_env->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue(null));

        $this->_commandLocator->expects($this->at(0))->method('locate')->with('a')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(1))->method('locate')->with('b')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(2))->method('locate')->with('c')->will($this->returnValue('/foo/bar/c'));

        $editor = new Editor($this->_commandLocator, ['a', 'b', 'c'], $this->_env);

        $this->assertSame('/foo/bar/c', $editor->get());
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
        $this->_env->expects($this->once())->method('getenv')->with('EDITOR')->will($this->returnValue(null));


        $this->_commandLocator->expects($this->at(0))->method('locate')->with('a')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(1))->method('locate')->with('b')->will($this->returnValue(null));
        $this->_commandLocator->expects($this->at(2))->method('locate')->with('c')->will($this->returnValue(null));

        $editor = new Editor($this->_commandLocator, ['a', 'b', 'c'], $this->_env);

        $this->assertNull($editor->get());
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
        $editor = $this->getMockBuilder('\Nubs\Sensible\Editor')->disableOriginalConstructor()->setMethods(['get'])->getMock();
        $editor->expects($this->once())->method('get')->will($this->returnValue($editorPath));

        $process = $this->getMockBuilder('\Symfony\Component\Process\Process')->disableOriginalConstructor()->setMethods(['setTty', 'run'])->getMock();
        $process->expects($this->once())->method('setTty')->with(true)->will($this->returnSelf());
        $process->expects($this->once())->method('run')->will($this->returnValue(0));

        $processBuilder = $this->getMockBuilder('\Symfony\Component\Process\ProcessBuilder')->disableOriginalConstructor()->setMethods(['setPrefix', 'setArguments', 'getProcess'])->getMock();
        $processBuilder->expects($this->once())->method('setPrefix')->with($editorPath)->will($this->returnSelf());
        $processBuilder->expects($this->once())->method('setArguments')->with([$filePath])->will($this->returnSelf());
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

        $process = $this->getMockBuilder('\Symfony\Component\Process\Process')->disableOriginalConstructor()->setMethods(['isSuccessful'])->getMock();
        $process->expects($this->once())->method('isSuccessful')->will($this->returnValue(true));

        $processBuilder = $this->getMockBuilder('\Symfony\Component\Process\ProcessBuilder')->disableOriginalConstructor()->setMethods([])->getMock();

        $editor = $this->getMockBuilder('\Nubs\Sensible\Editor')->disableOriginalConstructor()->setMethods(['editFile'])->getMock();
        $editor->expects($this->once())->method('editFile')->will($this->returnValue($process));

        $this->assertSame($data, $editor->editData($processBuilder, $data));
    }
}

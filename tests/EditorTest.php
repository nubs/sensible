<?php
namespace Nubs\Sensible;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Nubs\Sensible\Editor
 */
class EditorTest extends TestCase
{
    /**
     * Verify that editFile works.
     *
     * @test
     * @covers ::__construct
     * @covers ::editFile
     */
    public function editFile()
    {
        $editorPath = '/the/editor';
        $filePath = '/the/file';

        $editor = new Editor($editorPath);

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
     * @covers ::__construct
     * @covers ::editData
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

<?php

namespace PHPfriends\SimplePdf\Main;

class HighLevelTest extends \PHPUnit_Framework_TestCase
{
    public function testEntireDocument()
    {
        $tool = 'gs';
        $output = [];

        exec("which {$tool}", $output);

        if ((0 == count($output)) || in_array("{$tool} not found", $output)) {
            $this->markTestSkipped($tool . ' is not present, unable to test the validity of the PDF document');
        }

        $cacheFolder = dirname(dirname(__DIR__)) . '/cache/';

        $pdf = new HighLevelPdf(612.0, 792.0);

        $pdf->setFont('Neuton', 'Regular', 24);
        $pdf->setCell();
        $pdf->writeTextJustify('Hello world !');

        $pdf->saveToFile($cacheFolder . '/test.pdf');

        $output = [];
        exec("{$tool} -o /dev/null {$cacheFolder}/test.pdf", $output);

        $result = $this->processOutput($output);

        $errorMsg = join("\n", $result);
        $this->assertEmpty($errorMsg, '>>>>> ' . $errorMsg . ' <<<<<');
    }

    private function processOutput($output)
    {
        $result = [];
        foreach ($output as $item) {
            if (preg_match("/\*{4}\s+Error:\s+(?<message>.*)/", $item, $match)) {
                $result[] = $match['message'];
            }
        }

        return $result;
    }
}


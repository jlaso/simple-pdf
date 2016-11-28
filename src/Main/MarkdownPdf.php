<?php

namespace PHPfriends\SimplePdf\Main;

use PHPfriends\SimplePdf\Events\PageBreakEvent;

class MarkdownPdf extends HighLevelPdf
{
    protected $config;

    /**
     * RichText  /RV  => prints text in several formats.
     */
    protected $styles = [
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'p',
        'em',
        'code',
    ];

    /**
     * @param array $config
     * @param bool  $verbose
     */
    public function __construct(array $config, $verbose = false)
    {
        parent::__construct($config['paper_size']['width'], $config['paper_size']['height'], $verbose);
        foreach ($this->styles as $style) {
            $fName = $config[$style]['font']['name'];
            $fStyle = $config[$style]['font']['style'];
            $fSize = $config[$style]['font']['size'];
            $config[$style]['font']['font_key'] = $this->setFont($fName, $fStyle, $fSize);
        }
        $this->config = $config;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    private function markdown(&$text)
    {
        if (preg_match("/^\{pagebreak\}/", $text)) {
            $this->throwEvent(new PageBreakEvent($this->currentPage));
            $text = substr($text, strlen('{pagebreak}'));

            return true;
        }

        return false;
    }

    /**
     * returns true if in $text next thing is a double carriage return.
     *
     * @param $text
     *
     * @return bool
     */
    private function mdMatchDoubleNewLine(&$text)
    {
        if (substr($text, 0, 2) == "\n\n") {
            $text = substr($text, 2);

            return true;
        }
        if (substr($text, 0, 4) == "\r\n\r\n") {
            $text = substr($text, 4);

            return true;
        }

        return false;
    }

    private function mdSetFont($style)
    {
        $this->log('setting font for '.$style.' style.');
        $fontStyle = $this->config[$style]['font'];
        $this->selectFont($fontStyle['font_key'], $fontStyle['size']);
        if (isset($fontStyle['color']) && ($fontStyle['color'])) {
            $this->selectFontColor($fontStyle['color']);
        }
    }

    public function mdWriteTextJustify($text, $defaultLang = null)
    {
        //$toPrint = '';
        $mdFlow = new MarkdownFlow();
        $this->mdSetFont('p');

        while ($text && ($mdFlow->getStatus() != MarkdownFlow::END)) {
            $text = $mdFlow->ahead($text);
            if (!$mdFlow->getMatchedText()) {
                break;
            }

            switch ($mdFlow->getStatus()) {
                case MarkdownFlow::IN_HEADER:
                    $this->mdSetFont('h'.$mdFlow->getInfo());
                    $this->writeTextJustify($mdFlow->getMatchedText(), $defaultLang);
                    break;

                case MarkdownFlow::IN_PARAGRAPH:
                    $this->mdSetFont('p');
                    $this->writeTextJustify($mdFlow->getMatchedText(), $defaultLang);
                    break;
            }
            // interpret markdown
            if ($mdCode = $this->markdown($text)) {
                continue;
            }
//            if ($this->matchNewLine($text)) {
//                if (!$inParagraph || $this->matchNewLine($text)) {
//                    $this->writeTextJustify($toPrint, $defaultLang);
//                    $toPrint = '';
//                    $inParagraph = true;
//                    $this->mdSetFont('p');
//                }else{
//                    $toPrint .= ' ';
//                }
//            }
            //$ch = substr($text, 0, 1);
            //$toPrint .= $ch;
            //$text = substr($text, 1);
        }

//        if($toPrint){
//            $this->writeTextJustify($toPrint, $defaultLang);
//        }
    }
}

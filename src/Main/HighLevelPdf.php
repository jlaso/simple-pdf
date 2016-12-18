<?php

namespace PHPfriends\SimplePdf\Main;

use PHPfriends\SimplePdf\Adaptor\FontFile2FontDict;
use PHPfriends\SimplePdf\Adaptor\FontManager;
use PHPfriends\SimplePdf\Adaptor\HyphenatorInterface;
use PHPfriends\SimplePdf\Events\BaseEvent;
use PHPfriends\SimplePdf\Events\EventDispatcher;
use PHPfriends\SimplePdf\Events\LineBreakEvent;
use PHPfriends\SimplePdf\Exceptions\NotEnoughWidthToPrintException;
use PHPfriends\SimplePdf\HighLevelObjects\Font;
use PHPfriends\SimplePdf\HighLevelObjects\Page;
use PHPfriends\SimplePdf\HighLevelObjects\Rectangle;
use PHPfriends\SimplePdf\HighLevelObjects\TextCell;
use PHPfriends\SimplePdf\LowLevelParts\Box;
use PHPfriends\SimplePdf\LowLevelParts\Color;
use PHPfriends\SimplePdf\LowLevelParts\Content;
use PHPfriends\SimplePdf\LowLevelParts\FontDictTruetype;
use PHPfriends\SimplePdf\LowLevelParts\PageNode;
use PHPfriends\SimplePdf\LowLevelParts\PagesNode;
use PHPfriends\SimplePdf\LowLevelParts\PdfArray;
use PHPfriends\SimplePdf\LowLevelParts\PdfName;
use PHPfriends\SimplePdf\LowLevelParts\PdfNumber;
use PHPfriends\SimplePdf\LowLevelParts\ResourceNode;
use PHPfriends\SimplePdf\Measurement\FontMetrics;
use PHPfriends\SimplePdf\Util\PseudoXmlParser;
use PHPfriends\SimplePdf\Util\TextStatus;

class HighLevelPdf
{
    /** @var bool */
    protected $verbose;

    /** @var LowLevelPdf */
    protected $pdf;

    /** @var bool */
    protected $twoSided = true;

    // Margins

    /** @var float outer margin on twoSided or left margin */
    protected $outerMargin = 15.0;
    /** @var float inner margin on twoSided or right margin */
    protected $innerMargin = 25.0;
    /** @var float */
    protected $topMargin = 20.0;
    /** @var float */
    protected $bottomMargin = 20.0;

    /** @var float */
    protected $constantK = 1.0;
    /** @var float */
    protected $pageWidth = 297.0;
    /** @var float */
    protected $pageHeight = 214.97;
    /** @var int */
    protected $currentPageNum = 0;

    /** @var float */
    protected $currentY = 0.0;
    /** @var float */
    protected $currentX = 0.0;
    /** @var float */
    protected $currentWidth = 0.0;
    /** @var float */
    protected $currentHeight = 0.0;
    /** @var Font[] */
    protected $fonts;
    /** @var array */
    protected $fontsWidths;
    /** @var Font */
    protected $currentFont;
    /** @var Color|string */
    protected $currentFontColor;
    /** @var float */
    protected $currentFontSize;
    /** @var float */
    protected $currentFontHeight;
    /** @var Page[] */
    protected $pages;
    /** @var Page */
    protected $currentPage;
    /** @var array */
    protected $resources;

    /** @var EventDispatcher */
    protected $eventDispatcher;
    /** @var HyphenatorInterface */
    protected $hyphenator;

    /**
     * @param float $width
     * @param float $height
     */
    public function __construct($width, $height, $verbose = false)
    {
        $this->hyphenator = null;
        $this->pageWidth = $width;
        $this->pageHeight = $height;
        $this->eventDispatcher = new EventDispatcher();
        $this->verbose = $verbose;
        $this->pdf = new LowLevelPdf($verbose);
        $this->newPage();
    }

    /**
     * @param boolean $twoSided
     */
    public function setTwoSided($twoSided)
    {
        $this->twoSided = $twoSided;
    }

    /**
     * @return float
     */
    public function getCurrentFontHeight()
    {
        return $this->currentFontHeight;
    }

    /**
     * @param HyphenatorInterface $hyphenator
     */
    public function setHyphenator($hyphenator)
    {
        $this->hyphenator = $hyphenator;
    }

    protected function throwEvent(BaseEvent $event)
    {
        $this->eventDispatcher->dispatchEvent($event);
    }

    /**
     * transforms Y in order to maintain a virtual coordinate system based on
     * top-left corner is the (0,0) origin.
     *
     * @param float $y
     * @param float $h
     *
     * @return float
     */
    public function xformY($y, $h = 0.0)
    {
        return $this->pageHeight - $y - $h;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function newPage()
    {
        ++$this->currentPageNum;
        $this->log(sprintf('HighLevelPdf::newPage(currentPageNum=%s)', $this->currentPageNum));
        $this->currentPage = new Page($this->currentPageNum);
        $this->pages[$this->currentPageNum] = $this->currentPage;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setMetadata($key, $value)
    {
        if ('Keywords' === $key) {
            $this->pdf->setMetadataInfo('AAPL:Keywords', PdfArray::toPdfArrayStrings($value));
            $value = implode(',', $value);
        }
        $this->pdf->setMetadataInfo($key, $value);
    }

    /**
     * @param float $x
     * @param float $y
     * @param float $w
     * @param float $h
     *
     * @return $this
     */
    public function setCell($x = null, $y = null, $w = null, $h = null)
    {
        $this->currentX = (null === $x) ? $this->getLeftX() : $x;
        $this->currentY = (null === $y) ? $this->getTopY() : $y;
        $this->currentWidth = (null === $w) ? $this->getMaxWidth($this->currentX) : $w;
        $this->currentHeight = (null === $h) ? $this->currentFontHeight : $h;

        return $this;
    }

    /**
     * @param Font $font
     *
     * @return mixed
     */
    protected function getFontWidths(Font $font)
    {
        $name = $font->getName();
        $style = $font->getStyle();

        if (!isset($this->fontsWidths[$name][$style])) {
            $fontTool = new FontMetrics($name, $style);
            $this->fontsWidths[$name][$style] = $fontTool->getWidths();
        }

        return $this->fontsWidths[$name][$style];
    }

    /**
     * @param string $font
     * @param string $style
     * @param float  $size
     *
     * @return string
     */
    public function setFont($font, $style, $size)
    {
        $this->currentFont = new Font($font, $style);
        $this->currentFontHeight = $this->currentFont->getFontHeight($size);
        $key = $this->currentFont->getFontName();
        // store font in order to include later in PDF file
        $this->fonts[$key] = $this->currentFont;
        $this->resources[$this->currentPageNum]['Font'][$key] = true;
        $this->currentFontSize = $size;

        return $key;
    }

    /**
     * @param string       $fontKey
     * @param string       $size
     * @param Color|string $color
     */
    public function selectFont($fontKey, $size, $color = null)
    {
        $this->currentFont = $this->fonts[$fontKey];
        $this->resources[$this->currentPageNum]['Font'][$fontKey] = true;
        $this->currentFontSize = $size;
        if ($color) {
            $this->currentFontColor = $color;
        }
    }

    /**
     * @param Color|string $color
     */
    public function selectFontColor($color)
    {
        $this->currentFontColor = $color;
    }

    private function realSize($width, $add = 0)
    {
        // For all font types except Type 3, the units of glyph
        // space are one-thousandth of a unit of text space
        return $this->currentFontSize * ($add + $width) / 1000;
    }

    /**
     * @param string $lang
     * @param string $text
     *
     * @return string
     */
    private function hyphenate($lang, $text)
    {
        if ($lang && $this->hyphenator) {
            $this->hyphenator->setLanguage($lang);
            $text = $this->hyphenator->hyphenate($text);
        }

        return $text;
    }

    /**
     * returns true if in $text next thing is a carriage return.
     *
     * @param $text
     *
     * @return bool
     */
    protected function matchNewLine(&$text)
    {
        if (substr($text, 0, 1) == "\n") {
            $text = substr($text, 1);

            return true;
        }
        if (substr($text, 0, 2) == "\r\n") {
            $text = substr($text, 2);

            return true;
        }

        return false;
    }

    /**
     * @param string $toPrint
     * @param float  $width
     * @param bool   $justify
     */
    private function finishCurrentLine(&$toPrint, &$width, $justify = null)
    {
        $this->log("HighLevelPdf::finishCurrentLine('{$toPrint}',{$width},{$justify})");
        $t = new TextCell(
            $this->currentX,
            $this->xformY($this->currentY, $this->currentHeight),
            $this->currentWidth,
            $this->currentHeight,
            $this->currentFont,
            $this->currentFontSize,
            $toPrint
        );
        if ($justify) {
            $words = substr_count($toPrint, ' ');
            $delta = $words > 0 ? $width / $words : 0.0;
            $t->setWordSpace($delta);
        }
        if ($this->currentFontColor) {
            $t->setFontColor($this->currentFontColor);
        }
        $this->currentPage->addContent($t);
        $toPrint = '';
        $width = $this->currentWidth;
        $this->throwEvent(new LineBreakEvent($this->currentPage));
        $this->currentY += $this->currentFontHeight;
    }

    /**
     * @param string $text
     */
    public function writeText($text)
    {
        $t = new TextCell(
            $this->currentX,
            $this->xformY($this->currentY, $this->currentHeight),
            $this->currentWidth,
            $this->currentHeight,
            $this->currentFont,
            $this->currentFontSize,
            $text
        );
        if ($this->currentFontColor) {
            $t->setFontColor($this->currentFontColor);
        }
        $this->currentPage->addContent($t);
    }

    /**
     * @return TextStatus[]
     */
    private function makeFontSetAndWidths()
    {
        $baseFont = $this->currentFont;
        $size = $this->currentFontSize;
        $name = $baseFont->getName();
        $style = $baseFont->getStyle();

        /** @var TextStatus[] $fonts */
        $fonts = [];

        $fonts[PseudoXmlParser::NONE] = new TextStatus(
            $baseFont,
            $this->getFontWidths($baseFont),
            $size
        );

        $auxFont = new Font($name, 'Bold');

        $fonts[PseudoXmlParser::BOLD] = new TextStatus(
            $auxFont,
            $this->getFontWidths($auxFont),
            $size
        );

        $auxFont = new Font($name, 'Italic');

        $fonts[PseudoXmlParser::ITALIC] = new TextStatus(
            $auxFont,
            $this->getFontWidths($auxFont),
            $size
        );

        $auxFont = $baseFont;

        $fonts[PseudoXmlParser::LINK] = new TextStatus(
            $auxFont,
            $this->getFontWidths($auxFont),
            $size
        );

        $auxFont = $baseFont;
        $size /= 2;

        $fonts[PseudoXmlParser::SUPERSCRIPT] = new TextStatus(
            $auxFont,
            $this->getFontWidths($auxFont),
            $size,
            3 * $baseFont->getFontHeight($size)/2
        );

        $fonts[PseudoXmlParser::SUBSCRIPT] = $fonts[PseudoXmlParser::SUPERSCRIPT];
        $fonts[PseudoXmlParser::SUBSCRIPT]->hOffset *= -1;
        $fonts[PseudoXmlParser::SUBSCRIPT]->hOffset /= 2;

        return $fonts;
    }

    /**
     * @param string $text
     * @param string $hyphenateLang
     *
     * @return $this
     *
     * @throws NotEnoughWidthToPrintException
     */
    public function writeTextJustify($text, $hyphenateLang = null)
    {
        $this->log("HighLevelPdf::writeTextJustify('{$text}', '{$hyphenateLang}')");
        $text = $this->hyphenate($hyphenateLang, $text);
        $this->log("Hyphenated: '".str_replace('­'  /* <- invisible? &shy; */, '·', $text)."'");
        //$widths = $this->getFontWidths($this->currentFont);
        $width = $this->currentWidth;
        $toPrint = '';
        $hyphen = $this->hyphenator ? $this->hyphenator->getHyphen() : null;
        $parser = new PseudoXmlParser($this->makeFontSetAndWidths());
        $parser->switchStatus($this->currentFont, $this->currentFontSize, $widths);

        /**
         * @param string $text
         *
         * @return bool
         */
        $matchHyphen = function (&$text) use ($hyphen) {
            if ($hyphen && (substr($text, 0, strlen($hyphen)) == $hyphen)) {
                $text = substr($text, strlen($hyphen));

                return true;
            }

            return false;
        };

        /**
         * @param string|int $char
         *
         * @return float
         */
        $getRealWidth = function ($char) use ($widths) {
            $char = is_string($char) ? ord($char) : (int) $char;

            return $this->realSize(isset($widths[$char]) ? $widths[$char] : $widths[32]);
        };

        $lastHyphenable = null;
        $hyphenWidth = $getRealWidth('-');
        $spaceWidth = $getRealWidth(' ');
        $this->log("{ hyphen:{$hyphenWidth}, space:{$spaceWidth} }");
        $lineWidths = [];
        if ($width < $spaceWidth) {
            throw new NotEnoughWidthToPrintException("Width to print ({$width}) is not enough");
        }

        while (strlen($text) > 0) {
            // check if next thing is a carriage return in order to finish current line.
            if ($this->matchNewLine($text)) {
                $this->finishCurrentLine($toPrint, $width, false);
                $lineWidths = [];
            }
            // check if next thing is an hyphen in order to remember it just in case get end of line
            if ($matchHyphen($text)) {
                $lastHyphenable = strlen($toPrint);
                continue;
            }
            $ch = substr($text, 0, 1);
            if ($ch == ' ') {
                if (count($lineWidths) === 0) {  // don't print spaces at the beginning of the line
                    $text = substr($text, 1);
                    continue;
                }
                $lastHyphenable = strlen($toPrint) + 1;
            }
            $w = $getRealWidth($ch);

            // check if the char fits in the remaining room
            if ($w > $width) {
                $this->log($toPrint.' '.$lastHyphenable);
                if ($ch == ' ') {
                    $toPrint = substr($toPrint, 0, $lastHyphenable - 1);
                } else {
                    if ($lastHyphenable < strlen($toPrint) + 1) {
                        // caution, $lastHypenable could be null
                        $notHyphenable = substr($toPrint, $lastHyphenable);
                        $text = ltrim($notHyphenable.$text);
                        $this->log("notHyphenable = `{$notHyphenable}`");

                        $widthTilLstHyphble = array_sum(array_slice($lineWidths, $lastHyphenable));
                        $width += $widthTilLstHyphble;
                        $this->log("widthTilLstHyphble = {$widthTilLstHyphble}");
                        $toPrint = substr($toPrint, 0, $lastHyphenable);
                    }

                    $chAux = substr($toPrint, -1);

                    if ($chAux == ' ') {
                        $toPrint = substr($toPrint, 0, $lastHyphenable - 1);
                        $width += $spaceWidth;
                    } else {
                        $toPrint .= '-';
                        $width -= $hyphenWidth;
                    }
                }
                $this->finishCurrentLine($toPrint, $width, true);
                $lineWidths = [];
            } else {
                $toPrint .= $ch;
                $lineWidths[] = $w;
                $text = substr($text, 1);
                $width -= $w;
            }
        }

        if ($toPrint) {
            $this->finishCurrentLine($toPrint, $width, false);
        }

        return $this;
    }

    /**
     * @param string $text
     * @return float
     */
    public function getRealWidth($text)
    {
        $widths = $this->getFontWidths($this->currentFont);
        $getRealWidth = function ($char) use ($widths) {
            $char = is_string($char) ? ord($char) : (int) $char;

            return $this->realSize(isset($widths[$char]) ? $widths[$char] : $widths[32]);
        };
        $width = 0;
        for($i=0; $i<strlen($text); $i++) {
            $ch = substr($text, $i, 1);
            $width += $getRealWidth($ch);
        }

        return $width;
    }

    /**
     * @param float  $x
     * @param float  $y
     * @param float  $w
     * @param float  $h
     * @param string $color
     * @param int    $stroke
     */
    public function rectangle($x = null, $y = null, $w = null, $h = null, $color = '0 0 1', $stroke = 1)
    {
        $x = $x === null ? $this->getLeftX() : $x;
        $y = $y === null ? $this->getTopY() : $this->xformY($y);
        $w = $w === null ? $this->getMaxWidth($x) : $w;
        $h = $h === null ? $this->getMaxHeight($y) : $h;
        $rect = new Rectangle($x, $y, $w, $h, $color, $stroke);
        $this->currentPage->addContent($rect);
    }

    /**
     * @return float
     */
    public function getLeftX()
    {
        return !$this->twoSided || $this->oddPage() ? $this->innerMargin : $this->outerMargin;
    }

    /**
     * @return float
     */
    public function getRightX()
    {
        return $this->pageWidth -
        (!$this->twoSided || $this->oddPage()
            ? $this->outerMargin
            : $this->innerMargin
        );
    }

    /**
     * @return float
     */
    public function getTopY()
    {
        return $this->topMargin;
    }

    /**
     * @return float
     */
    protected function getBottomY()
    {
        return $this->pageHeight - $this->bottomMargin;
    }

    /**
     * @param float $currX
     *
     * @return float
     */
    public function getMaxWidth($currX = null)
    {
        return $this->getRightX() - (
        (null === $currX)
            ? $this->getLeftX()
            : $currX
        );
    }

    /**
     * @return float
     */
    public function getPageWidth()
    {
        return $this->pageWidth;
    }

    /**
     * @param float $currY
     *
     * @return float
     */
    public function getMaxHeight($currY = null)
    {
        return $this->getBottomY() - (
        (null === $currY)
            ? $this->getTopY()
            : $currY
        );
    }

    /**
     * @return bool
     */
    protected function oddPage()
    {
        return (bool) ($this->currentPageNum % 2);
    }

    /**
     * @param float $value
     *
     * @return float
     */
    protected function byK($value)
    {
        return $this->constantK * $value;
    }

    private function process()
    {
        $fonts = [];

        // add fonts as resources
        foreach ($this->fonts as $key => $font) {
            if (!isset($fonts[$key])) {
                $fonts[$key] = $this->handleFont($key, $font);
            }
        }

        $pagesNode = new PagesNode();
        $this->pdf->addObject($pagesNode);

        foreach ($this->pages as $page) {
            $this->log('HighLevelPdf::process adding page '.$page->getPageNum());
            $pageResources = new ResourceNode();
            $contents = $page->getContents();
            if (count($contents) > 0) {
                $pageResources->addProcSet('Text');
            }
            if (count($this->resources[$page->getPageNum()]['Font']) > 0) {
                $pageResources->addProcSet('PDF');
                foreach ($this->resources[$page->getPageNum()]['Font'] as $fontKey => $used) {
                    if ($used) {
                        $pageResources->addFont($fonts[$fontKey]);
                    }
                }
            }
            $this->pdf->addObject($pageResources);

            $pageContents = new Content();
            $pageNode = new PageNode($pagesNode, $pageResources, new Box(0, 0, $this->pageWidth, $this->pageHeight));
            // @TODO: process graphics
            if (count($contents) > 0) {
                foreach ($contents as $content) {
                    $content->addToContent($pageContents);
                }
            }
            $pageNode->setContents($pageContents);
            $this->pdf->addObject($pageContents);
            $this->pdf->addObject($pageNode);
        }
    }

    /**
     * @param string $key
     * @param Font   $font
     *
     * @return FontDictTruetype
     */
    private function handleFont($key, Font $font)
    {
        $ff2fd = new FontFile2FontDict($font->getName(), $font->getStyle());

        $widths = $ff2fd->getWidths();
        $this->pdf->addObject($widths);

        $fontDescriptor = $ff2fd->getFontDescriptor();
        foreach ($fontDescriptor->getItems() as $item) {
            if (method_exists($item, 'getReference')) {
                $this->pdf->addObject($item);
            }
        }
        $this->pdf->addObject($fontDescriptor);

        $fontDict = new FontDictTruetype($key, $ff2fd->getBaseName());
        $fontDict->addItem('Widths', $widths);
        $fontDict->addItem('FirstChar', new PdfNumber(32));
        $fontDict->addItem('LastChar', new PdfNumber($widths->getLength() + 32 - 1));
        $fontDict->addItem('FontDescriptor', $fontDescriptor);
        $fontDict->addItem('Encoding', new PdfName('MacRomanEncoding'));
        //$fontDict->addItem('Encoding', new PdfName('WinAnsiEncoding'));
        $this->pdf->addObject($fontDict);

        return $fontDict;
    }

    /**
     * @param string $fileName
     */
    public function saveToFile($fileName)
    {
        $this->process();
        $this->pdf->saveToFile($fileName);
        $this->log("Fonts used:\r\n\r\n".var_export(FontManager::getInstance()->getAliases(), true)."\r\n");
    }

    /**
     * @param string $text
     */
    protected function log($text)
    {
        if ($this->verbose) {
            echo $text.PHP_EOL;
        }
    }
}

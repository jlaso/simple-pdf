<?php

namespace PHPfriends\SimplePdf\Main;

use PHPfriends\SimplePdf\Adaptor\FontFile2FontDict;
use PHPfriends\SimplePdf\Events\EventDispatcher;
use PHPfriends\SimplePdf\Main\Objects\Font;
use PHPfriends\SimplePdf\Main\Objects\Page;
use PHPfriends\SimplePdf\Main\Objects\TextCell;
use PHPfriends\SimplePdf\Parts\Box;
use PHPfriends\SimplePdf\Parts\Dictionary;
use PHPfriends\SimplePdf\Parts\FontDescriptorDict;
use PHPfriends\SimplePdf\Parts\FontDict;
use PHPfriends\SimplePdf\Parts\FontDictTruetype;
use PHPfriends\SimplePdf\Parts\NeedsObject;
use PHPfriends\SimplePdf\Parts\PageNode;
use PHPfriends\SimplePdf\Parts\PagesNode;
use PHPfriends\SimplePdf\Parts\PdfArray;
use PHPfriends\SimplePdf\Parts\PdfName;
use PHPfriends\SimplePdf\Parts\PdfNumber;
use PHPfriends\SimplePdf\Parts\ResourceNode;

class HighLevelPdf
{
    /** @var LowLevelPdf */
    protected $pdf;

    /** @var bool */
    protected $twoSided = true;

    # Margins

    /** @var float outer margin on twoSided or left margin */
    protected $outerMargin = 15.0;
    /** @var float inner marging on twoSided or right margin */
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
    /** @var Font */
    protected $currentFont;
    /** @var float */
    protected $currentFontSize;
    /** @var Page[] */
    protected $pages;
    /** @var Page */
    protected $currentPage;
    /** @var array */
    protected $resources;

    /** @var EventDispatcher */
    protected $eventDispatcher;

    /**
     * @param float $width
     * @param float $height
     */
    public function __construct($width, $height)
    {
        $this->pageWidth = $width;
        $this->pageHeight = $height;
        $this->eventDispatcher = new EventDispatcher();
        $this->pdf = new LowLevelPdf();
        $this->newPage();
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
        $this->currentPage++;
        $this->currentPage = new Page($this->currentPageNum);
        $this->pages[$this->currentPageNum] = $this->currentPage;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setMetadata($key, $value)
    {
        $this->pdf->setMetadataInfo($key, $value);
    }

    /**
     * @param float $x
     * @param float $y
     * @param float $w
     * @param float $h
     * @return $this
     */
    public function setCell($x = null, $y = null, $w = null, $h = null)
    {
        $this->currentX = (null === $x) ? $this->getLeftX() : $x;
        $this->currentY = (null === $y) ? $this->getTopY() : $y;
        $this->currentWidth = (null === $w) ? $this->getMaxWidth($this->currentX) : $w;
        $this->currentHeight = (null === $h) ?: $h;

        return $this;
    }

    /**
     * @param string $font
     * @param string $style
     * @param float $size
     * @return $this
     */
    public function setFont($font, $style, $size)
    {
        $this->currentFont = new Font($font, $style);
        $key = $this->currentFont->getFontName();
        // store font in order to include later in PDF file
        $this->fonts[$key] = $this->currentFont;
        $this->resources[$this->currentPageNum]['Font'][$key] = true;
        $this->currentFontSize = $size;

        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function writeTextJustify($text)
    {
        $t = new TextCell(
            $this->currentX,
            $this->currentY,
            $this->currentWidth,
            $this->currentHeight,
            $this->currentFont,
            $this->currentFontSize,
            $text
        );

        $this->currentPage->addContent($t);

        return $this;
    }

    /**
     * @return float
     */
    protected function getLeftX()
    {
        return !$this->twoSided || $this->oddPage() ? $this->innerMargin : $this->outerMargin;
    }

    /**
     * @return float
     */
    protected function getRightX()
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
    protected function getTopY()
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
     * @return float
     */
    protected function getMaxWidth($currX)
    {
        return $this->getRightX() - (
        (null === $currX)
            ? $this->getLeftX()
            : $currX
        );
    }

    /**
     * @param float $currY
     * @return float
     */
    protected function getMaxHeight($currY)
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
        return (boolean)($this->currentPageNum % 2);
    }

    /**
     * @param float $value
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
        foreach($this->fonts as $key => $font){
            if(!isset($fonts[$key])) {
                $fonts[$key] = $this->handleFont($key, $font);
            }
        }

        $pagesNode = new PagesNode();
        $this->pdf->addObject($pagesNode);

        foreach($this->pages as $page){
            $pageResources = new ResourceNode();
            foreach($this->resources[$page->getPageNum()]['Font'] as $fontKey => $used) {
                if($used) {
                    $pageResources->addFont($fonts[$fontKey]);
                }
            }
            $this->pdf->addObject($pageResources);

            $pageNode = new PageNode($pagesNode, $pageResources, new Box(0, 0, $this->pageWidth, $this->pageHeight));
            foreach($page->getContents() as $content){
                $c = $content->dump($this->pdf);
                $pageNode->setContents($c);
                $this->pdf->addObject($c);
            }
            $this->pdf->addObject($pageNode);
        }
    }

    private function handleFont($key, Font $font)
    {
        $ff2fd = new FontFile2FontDict($font->getName(), $font->getStyle());

        $widths = $ff2fd->getWidths();
        $this->pdf->addObject($widths);

        $fontDescriptor = $ff2fd->getFontDescriptor();
        foreach($fontDescriptor->getItems() as $item) {
            if(method_exists($item,'getReference')) {
                $this->pdf->addObject($item);
            }
        }
        $this->pdf->addObject($fontDescriptor);

        $fontDict = new FontDictTruetype($key, $ff2fd->getBaseName());
        $fontDict->addItem('Widths', $widths);
        $fontDict->addItem('FirstChar', new PdfNumber(32));
        $fontDict->addItem('LastChar', new PdfNumber($ff2fd->getWidthsLength() + 32 + 1));
        $fontDict->addItem('FontDescriptor', $fontDescriptor);
        $fontDict->addItem('Encoding', new PdfName('MacRomanEncoding'));
        $this->pdf->addObject($fontDict);

        //$resources = new ResourceNode();
        //$resources->addFont($fontDict);
        //$this->pdf->addObject($resources);

        return $fontDict;
    }


    /**
     * @param string $fileName
     */
    public function saveToFile($fileName)
    {
        $this->process();

        $this->pdf->saveToFile($fileName);
    }
}
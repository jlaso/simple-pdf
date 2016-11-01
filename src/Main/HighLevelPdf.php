<?php

namespace PHPfriends\SimplePdf\Main;

use PHPfriends\SimplePdf\Adaptor\FontFile2FontDict;
use PHPfriends\SimplePdf\Adaptor\FontManager;
use PHPfriends\SimplePdf\Events\EventDispatcher;
use PHPfriends\SimplePdf\HighLevelObjects\Font;
use PHPfriends\SimplePdf\HighLevelObjects\Page;
use PHPfriends\SimplePdf\HighLevelObjects\TextCell;
use PHPfriends\SimplePdf\LowLevelParts\Box;
use PHPfriends\SimplePdf\LowLevelParts\Content;
use PHPfriends\SimplePdf\LowLevelParts\FontDictTruetype;
use PHPfriends\SimplePdf\LowLevelParts\PageNode;
use PHPfriends\SimplePdf\LowLevelParts\PagesNode;
use PHPfriends\SimplePdf\LowLevelParts\PdfArray;
use PHPfriends\SimplePdf\LowLevelParts\PdfName;
use PHPfriends\SimplePdf\LowLevelParts\PdfNumber;
use PHPfriends\SimplePdf\LowLevelParts\ResourceNode;

class HighLevelPdf
{
    /** @var boolean */
    protected $verbose;

    /** @var LowLevelPdf */
    protected $pdf;

    /** @var bool */
    protected $twoSided = true;

    # Margins

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
    /** @var Font */
    protected $currentFont;
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

    /**
     * @param float $width
     * @param float $height
     */
    public function __construct($width, $height, $verbose = false)
    {
        $this->pageWidth = $width;
        $this->pageHeight = $height;
        $this->eventDispatcher = new EventDispatcher();
        $this->verbose = $verbose;
        $this->pdf = new LowLevelPdf($verbose);
        $this->newPage();
    }

    /**
     * transforms Y in order to maintain a virtual coordinate system based on
     * top-left corner is the (0,0) origin
     *
     * @param float $y
     * @param float $h
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
        $this->currentPage++;
        $this->currentPage = new Page($this->currentPageNum);
        $this->pages[$this->currentPageNum] = $this->currentPage;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setMetadata($key, $value)
    {
        if ('Keywords' === $key) {
            $this->pdf->setMetadataInfo('AAPL:Keywords', PdfArray::toPdfArrayStrings($value));
            $value = join(',', $value);
        }
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
        $this->currentHeight = (null === $h) ? $this->currentFontHeight : $h;

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
        $this->currentFontHeight = $this->currentFont->getFontHeight($size);
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
        $y = $this->xformY($this->currentY, $this->currentHeight);

        $t = new TextCell(
            $this->currentX,
            $y,
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
        foreach ($this->fonts as $key => $font) {
            if (!isset($fonts[$key])) {
                $fonts[$key] = $this->handleFont($key, $font);
            }
        }

        $pagesNode = new PagesNode();
        $this->pdf->addObject($pagesNode);

        foreach ($this->pages as $page) {
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
     * @param Font $font
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

        if ($this->verbose) {
            print "Fonts used:\r\n\r\n" .
                var_export(FontManager::getInstance()->getAliases(), true) .
                "\r\n\r\n";
        }
    }
}
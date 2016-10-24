<?php

namespace PHPfriends\SimplePdf\Main;

use PHPfriends\SimplePdf\Events\EventDispatcher;
use PHPfriends\SimplePdf\Main\Objects\Font;
use PHPfriends\SimplePdf\Main\Objects\Page;
use PHPfriends\SimplePdf\Parts\Box;
use PHPfriends\SimplePdf\Parts\Font as FontDict;
use PHPfriends\SimplePdf\Parts\PageNode;
use PHPfriends\SimplePdf\Parts\PagesNode;
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
    protected $currentPageNum = 1;

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
    /** @var Page[] */
    protected $pages;
    /** @var Page */
    protected $currentPage;

    /** @var EventDispatcher */
    protected $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->pdf = new LowLevelPdf();
        $this->pages[$this->currentPageNum] = new Page();
    }

    public function newPage()
    {
        $this->pages[++$this->currentPageNum] = new Page();
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
     */
    public function setFont($font, $style, $size)
    {
        $this->currentFont = new Font($font, $style, $size);
        $font = str_replace(' ','_',trim($font));
        $key = sprintf("%s_%s", ucwords(strtolower($font)), strtolower($style));
        // store font in order to include later in PDF file
        $this->fonts[$key] = $this->currentFont;
    }

    public function writeTextJustify($text)
    {
        // @TODO

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
        return (boolean)($this->currentPage % 2);
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
        foreach($this->fonts as $key => $font){
            $fontDict = new FontDict($key, FontDict::TRUETYPE, $font->getName());
            $this->pdf->addObject($fontDict);
            $resources = new ResourceNode();
            $resources->addFont($fontDict);
            $this->pdf->addObject($resources);
        }

        $pagesNode = new PagesNode();
        $this->pdf->addObject($pagesNode);

        foreach($this->pages as $page){
            $pageNode = new PageNode($pagesNode, $resources, new Box(0, 0, $this->pageWidth, $this->pageHeight));
            foreach($page->getContents() as $content){
                $pageNode->setContents($content);
            }
            $this->pdf->addObject($pageNode);
        }
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
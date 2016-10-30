<?php


namespace PHPfriends\SimplePdf\LowLevelParts;

use PHPfriends\SimplePdf\Common\GetAliasInterface;

class Content implements PartInterface
{
    use LazyReferenceTrait;

    /** @var string */
    protected $stream;

    /**
     */
    public function __construct()
    {
        $this->stream = '';
    }

    /**
     * @param string $stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    public function addStream($stream)
    {
        $this->stream .= $stream;
    }

    /**
     * @param float $x
     * @param float $y
     * @param GetAliasInterface|string $font
     * @param float $fontSize
     * @param string $text
     */
    public function addText($x, $y, $font, $fontSize, $text)
    {
        $fontName = ($font instanceof GetAliasInterface) ? $font->getAlias() : $font;
        $this->stream .= sprintf(
            "BT\r\n%.2F %.2F Td\r\n/%s %d Tf\r\n(%s) Tj\r\nET\r\n",
            $x, $y,
            $fontName, $fontSize,
            $text
        );
    }

    /**
     * @return string
     */
    public function dump()
    {
        $header = new Dictionary();

        $result = "stream\r\n".trim($this->stream)."\r\nendstream\r\n";

        $header->addItem('Length', new PdfNumber(strlen($this->stream)));

        $result = $header->dump()."\r\n".$result;

        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'Content '.md5($this->dump());
    }

}
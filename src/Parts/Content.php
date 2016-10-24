<?php


namespace PHPfriends\SimplePdf\Parts;


class Content implements PartInterface
{
    use LazyReferenceTrait;

    /** @var Dictionary */
    protected $header;
    /** @var string */
    protected $stream;

    /**
     */
    public function __construct()
    {
        $this->stream = '';
        $this->header = new Dictionary();
    }

    /**
     * @param string $stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    public function addText($x,$y,Font $font, $fontSize,$text)
    {
        $this->stream .= sprintf(
            "BT\r\n%.2F %.2F Td\r\n/%s %d Tf\r\n(%s) Tj\r\nET\r\n",
            $x, $y,
            $font->getName(), $fontSize,
            $text
        );
    }

    /**
     * @return string
     */
    public function dump()
    {
        $result = "stream\r\n".$this->stream."\r\nendstream\r\n";

        $this->header->addItem('Length', new PdfNumber(strlen($this->stream)));

        $result = $this->header->dump()."\r\n".$result;

        return $result;
    }


}
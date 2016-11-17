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
     * @param array $options
     */
    public function addText($x, $y, $font, $fontSize, $text, $options = [])
    {
        $fontName = ($font instanceof GetAliasInterface) ? $font->getAlias() : $font;
        $this->stream .= sprintf(
            "BT\r\n%.2F %.2F Td\r\n/%s %d Tf\r\n%s(%s) Tj\r\nET\r\n",
            $x, $y,
            $fontName, $fontSize,
            $this->developOptions($options),
            $text
        );
    }

    /**
     * @param array $options
     * @return string
     */
    private function developOptions($options)
    {
        $result = '';
        foreach ($options as $optionName => $optionValue){
            switch ($optionName){
                case 'word_spacing':
                    $result .= $optionValue." Tw\n";
                    break;
                case 'char_spacing':
                    $result .= $optionValue." Tc\n";
                    break;
            }
        }

        return $result;
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
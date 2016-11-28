<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

use PHPfriends\SimplePdf\Common\GetAliasInterface;
use PHPfriends\SimplePdf\Exceptions\OptionValueFormatException;

class Content implements PartInterface
{
    use LazyReferenceTrait;

    /** @var string */
    protected $stream;

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
     * @param float                    $x
     * @param float                    $y
     * @param GetAliasInterface|string $font
     * @param float                    $fontSize
     * @param string                   $text
     * @param array                    $options
     */
    public function addText($x, $y, $font, $fontSize, $text, $options = [])
    {
        $fontName = ($font instanceof GetAliasInterface) ? $font->getAlias() : $font;
        $this->stream .= sprintf(
            "BT\r\n%.2F %.2F Td\r\n/%s %d Tf\r\n%s(%s) Tj\r\nET\r\n",
            $x,
            $y,
            $fontName,
            $fontSize,
            $this->developOptions($options),
            $text
        );
    }

    /**
     * @param float  $x
     * @param float  $y
     * @param float  $w
     * @param float  $h
     * @param string $color
     * @param int    $stroke
     */
    public function addRectangle($x, $y, $w, $h, $color = '0 0 0', $stroke = 4)
    {
        $this->stream .= sprintf(
            "%d w\r\nq\r\n%s RG\r\n%f %f %f %f re\r\nS\r\nQ\r\n",
            $stroke,
            $color,
            $x,
            $y,
            $w,
            $h
        );
    }

    /**
     * @param array $options
     *
     * @return string
     *
     * @throws OptionValueFormatException
     */
    private function developOptions($options)
    {
        $result = '';
        foreach ($options as $optionName => $optionValue) {
            switch ($optionName) {
                case 'word_spacing':
                    $result .= $optionValue." Tw\n";
                    break;
                case 'char_spacing':
                    $result .= $optionValue." Tc\n";
                    break;
                case 'font_color':
                    if (is_string($optionValue)) {
                        $optionValue = new Color($optionValue);
                    }
                    if ($optionValue instanceof Color) {
                        $result .= $optionValue->dump()."\n";
                    } else {
                        throw new OptionValueFormatException('Value for `font_color` must be Color type or string');
                    }
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

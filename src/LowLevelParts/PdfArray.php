<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

class PdfArray implements PartInterface
{
    /** @var PartInterface[] */
    protected $values = [];

    /**
     * @param string[] $names
     * @return PdfArray
     */
    public static function toPdfArrayNames($names)
    {
        $result = new PdfArray();
        foreach($names as $name){
            $result->addItem(new PdfName($name));
        }

        return $result;
    }

    /**
     * @param string[] $strings
     * @return PdfArray
     */
    public static function toPdfArrayStrings($strings)
    {
        $result = new PdfArray();
        foreach($strings as $string){
            $result->addItem(new PdfString($string));
        }

        return $result;
    }

    public function addItem(PartInterface $item)
    {
        $this->values[] = $item;

        return $this;
    }

    /**
     * @param string $key
     * @param PartInterface $item
     * @return $this
     */
    public function addAssocItem($key, PartInterface $item)
    {
        $this->values[$key] = $item;

        return $this;
    }

    public function dump()
    {
        $result = '[ ';
        foreach($this->values as $value){
            $result .= $value->dump() . ' ';
        }
        $result .= ']';

        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'PdfArray '.md5($this->dump());
    }
}
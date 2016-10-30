<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

use PHPfriends\SimplePdf\Exceptions\ValueNotValidOnDictException;

class Widths implements PartInterface
{
    use LazyReferenceTrait;

    /** @var int[] */
    protected $values;

    /**
     * @param int[] $values
     * @throws ValueNotValidOnDictException
     */
    public function __construct($values)
    {
        foreach($values as $value){
            if(!is_numeric($value)){
                throw new ValueNotValidOnDictException("Widths expects all values of type numeric");
            }
        }
        $this->values = $values;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return count($this->values);
    }

    /**
     * @return string
     */
    public function dump()
    {
        return '[ ' . join(' ', $this->values). ' ]';
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return 'Widths '.md5($this->dump());
    }
}
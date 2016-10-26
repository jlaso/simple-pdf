<?php

namespace PHPfriends\SimplePdf\Parts;

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
                throw new ValueNotValidOnDictException()
            }
        }
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function dump()
    {
        return '[ ' . join(' ', $this->values). ' ]';
    }
}
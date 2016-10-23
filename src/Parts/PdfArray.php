<?php

namespace JLaso\SimplePdf\Parts;

class PdfArray implements PartInterface
{
    /** @var PartInterface[] */
    protected $values = [];

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

}
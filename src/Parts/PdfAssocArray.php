<?php

namespace JLaso\SimplePdf\Parts;

class PdfAssocArray implements PartInterface
{
    /** @var PartInterface[] */
    protected $values = [];

    /**
     * @param string $key
     * @param PartInterface $item
     * @return $this
     */
    public function addItem($key, PartInterface $item)
    {
        $this->values[$key] = $item;

        return $this;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $result = '[';
        foreach($this->values as $key => $value){
            $result .= sprintf("\r\n(%s) %s", $key, $value->dump());
        }
        $result .= "\r\n]";

        return $result;
    }

}
<?php

namespace PHPfriends\SimplePdf\Parts;

class Dictionary extends AbstractPart implements PartInterface
{
    const TYPE = null;

    /** @var PartInterface[] */
    protected $data = [];

    public function addItem($name, PartInterface $item)
    {
        $this->data[$name] = $item;

        return $this;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $result = '<<';
        if (count($this->data) > 0) {
            $result .= "\r\n";
            if (static::TYPE) {
                $result .= sprintf("/Type %s\r\n", static::TYPE);
            }
            foreach ($this->data as $name => $item) {
                if(method_exists($item, 'getReference')){
                    $item = $item->getReference();
                }
                $result .= sprintf("/%s %s\r\n", $name, $item->dump());
            }
        }
        $result .= '>>';

        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'Catalog '.md5($this->dump());
    }
}
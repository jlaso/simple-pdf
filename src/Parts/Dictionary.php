<?php

namespace PHPfriends\SimplePdf\Parts;

class Dictionary extends AbstractPart implements PartInterface
{
    const TYPE = null;

    /** @var PartInterface[] */
    protected $data = [];

    public function __construct()
    {
        if (static::TYPE) {
            $this->setType(static::TYPE);
        }
    }

    public function addItem($name, PartInterface $item)
    {
        $this->data[$name] = $item;

        return $this;
    }

    /**
     * @param string $type
     * @return Dictionary
     */
    public function setType($type)
    {
        return $this->addItem('Type', new PdfName($type));
    }

    /**
     * @return string
     */
    public function dump()
    {
        $result = '<<';
        if (count($this->data) > 0) {
            $result .= "\r\n";
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
}
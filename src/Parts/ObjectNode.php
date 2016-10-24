<?php

namespace PHPfriends\SimplePdf\Parts;

class ObjectNode extends AbstractPart implements PartInterface
{
    /** @var int */
    protected $id;
    /** @var int */
    protected $generation;
    /** @var PartInterface */
    protected $content;

    /**
     * @param int $id
     * @param int $generation
     */
    public function __construct($id, $generation = 0)
    {
        $this->id = $id;
        $this->generation = $generation;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param PartInterface $content
     */
    public function setContent(PartInterface $content)
    {
        $this->content = $content;
    }

    /**
     * @return Reference
     */
    public function getReference()
    {
        return new Reference($this->id, $this->generation);
    }

    /**
     * @return string
     */
    public function dump()
    {
        return sprintf(
            "%d %d obj\r\n%s\r\nendobj\r\n",
            $this->id,
            $this->generation,
            rtrim($this->content->dump())
        );
    }


}
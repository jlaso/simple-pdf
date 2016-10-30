<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

class ObjectNode extends AbstractPart implements PartInterface
{
    /** @var int */
    protected $id;
    /** @var int */
    protected $generation;
    /** @var PartInterface */
    protected $content;
    /** @var bool */
    protected $verbose;

    /**
     * @param int $id
     * @param int $generation
     */
    public function __construct($id, $generation = 0, $verbose = false)
    {
        $this->id = $id;
        $this->generation = $generation;
        $this->verbose = $verbose;
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
        $comment = '';
        if($this->verbose){
            $comment = method_exists($this->content,'getComment') ?
                $this->content->getComment() :
                preg_replace("/PHPfriends\\\SimplePdf\\\/", "", get_class($this->content));
            $comment = "   % {$comment}";
        }
        return sprintf(
            "%d %d obj%s\r\n%s\r\nendobj\r\n",
            $this->id,
            $this->generation,
            $comment,
            rtrim($this->content->dump())
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("Object: %d %d R", $this->id, $this->generation);
    }
}
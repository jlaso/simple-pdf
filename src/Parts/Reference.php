<?php

namespace JLaso\SimplePdf\Parts;

class Reference implements PartInterface
{
    /** @var Int */
    protected $id;
    /** @var Int */
    protected $generation;

    /**
     * @param Int $id
     * @param Int $generation
     */
    public function __construct($id, $generation = 0)
    {
        $this->id = $id;
        $this->generation = $generation;
    }

    public function dump()
    {
        return sprintf("%d %d R", $this->id, $this->generation);
    }
}
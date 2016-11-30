<?php

namespace PHPfriends\SimplePdf\Util;

final class Stack
{
    /** @var array */
    private $data = [];

    /**
     * @param mixed $item
     */
    public function push($item)
    {
        array_unshift($this->data, $item);
    }

    /**
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function pop()
    {
        if ($this->isEmpty()) {
            throw new \RuntimeException('This stack is empty');
        } else {
            return array_shift($this->data);
        }
    }

    /**
     * @return int
     */
    public function top()
    {
        return current($this->data);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return 0 == count($this->data);
    }
}

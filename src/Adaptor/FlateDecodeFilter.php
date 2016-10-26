<?php

namespace PHPfriends\SimplePdf\Adaptor;

class FlateDecodeFilter implements FilterInterface
{
    /**
     * @param string $data
     * @return string
     */
    public function filter($data)
    {
        return gzcompress($data);
    }
}
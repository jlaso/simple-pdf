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
        return gzcompress($data, 9, ZLIB_ENCODING_DEFLATE);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'FlateDecode';
    }
}
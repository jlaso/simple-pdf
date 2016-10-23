<?php

namespace JLaso\SimplePdf\Parts;

abstract class AbstractPart
{
    /**
     * Dumps a PDF block
     * @param string $block
     * @param array $data
     * @return string
     */
    protected function dumpBlock($block, $data)
    {
        $result = '';
        foreach ($data as $key => $value) {
            if ($value instanceof PartInterface) {
                $value = $value->dump();
            }
            $result .= sprintf("/%s %s\r\n", $key, $value);
        }

        return sprintf("%s\r\n<<\r\n%s>>", $block, $result);
    }
}
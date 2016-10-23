<?php


namespace JLaso\SimplePdf\Parts;


class CrossRefTable implements PartInterface
{
    protected $table;

    /**
     * @param int $index
     * @param int $item
     */
    public function addItem($index, $item)
    {
        $this->table[$index] = intval($item);
    }

    /**
     * @return string
     */
    public function dump()
    {
        $table = $this->table;
        ksort($table);
        $result = sprintf("xref\r\n0 %d\r\n", 1+count($this->table));
        $result .= "0000000000 65535 f\r\n";
        foreach($this->table as $item){
            $result .= sprintf("%'010d 00000 n\r\n", $item);
        }

        return $result;
    }
}
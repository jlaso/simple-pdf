<?php


namespace PHPfriends\SimplePdf\Parts;


use PHPfriends\SimplePdf\Adaptor\FilterInterface;
use PHPfriends\SimplePdf\Adaptor\FlateDecodeFilter;
use PHPfriends\SimplePdf\Exceptions\ToDoException;

class FontFileStream implements PartInterface
{
    use LazyReferenceTrait;

    /** @var string */
    protected $fontFile;
    /** @var string */
    protected $filter;

    /**
     * @param string $fontFile
     * @param string $filter
     * @throws ToDoException
     */
    public function __construct($fontFile, $filter = FilterInterface::FlateDecode)
    {
        $this->fontFile = $fontFile;
        $this->filter = $filter;

        if(FilterInterface::FlateDecode !== $filter){
            throw new ToDoException("Filter `{$filter}` is not implemented yet");
        }

    }

    /**
     * @return string
     */
    public function dump()
    {
        $header = new Dictionary();

        $length = filesize($this->fontFile);
        $file = fopen($this->fontFile,'rb');
        $stream = fread($file, $length);
        fclose($file);
        //$stream = file_get_contents($this->fontFile);

        $header->addItem('Length1', new PdfNumber($length));

        $filter = new FlateDecodeFilter();
        $stream = $filter->filter($stream);

        $header->addItem('Length', new PdfNumber(strlen($stream)));

        return $header->dump()."\r\n".
            "stream\r\n{$stream}\r\nendstream\r\n";
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'FONT::'.$this->fontFile;
    }
}
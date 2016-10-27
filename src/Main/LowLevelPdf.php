<?php

namespace PHPfriends\SimplePdf\Main;

use PHPfriends\SimplePdf\Exceptions\ReferenceNotResolved;
use PHPfriends\SimplePdf\Parts\Catalog;
use PHPfriends\SimplePdf\Parts\CrossRefTable;
use PHPfriends\SimplePdf\Parts\MetadataDict;
use PHPfriends\SimplePdf\Parts\NamesCatalog;
use PHPfriends\SimplePdf\Parts\ObjectNode;
use PHPfriends\SimplePdf\Parts\PagesNode;
use PHPfriends\SimplePdf\Parts\PartInterface;
use PHPfriends\SimplePdf\Parts\PdfString;
use PHPfriends\SimplePdf\Parts\Trailer;

class LowLevelPdf
{
    const VERSION = '1.3';

    /** @var ObjectNode[] */
    protected $objects;
    /** @var Catalog */
    protected $masterCatalog;
    /** @var NamesCatalog */
    protected $namesCatalog;
    /** @var Trailer */
    protected $trailer;
    /** @var MetadataDict */
    protected $metadataDict;

    /**
     */
    public function __construct()
    {
        $this->objects = [];
        $this->masterCatalog = new Catalog();
        $this->addObject($this->masterCatalog);
        $this->namesCatalog = new NamesCatalog();
        //$this->addObject($this->namesCatalog);
        $this->trailer = new Trailer();   // Trailer is not an object
        $this->metadataDict = new MetadataDict();
        $this->addObject($this->metadataDict);
    }

    /**
     * @param PartInterface $obj
     * @return int
     */
    public function addObject($obj)
    {
        $id = count($this->objects) + 1;
        $object = new ObjectNode($id);
        $object->setContent($obj);
        if (method_exists($obj, 'injectObject')) {
            $obj->injectObject($object);
        }
        $this->objects[$id] = $object;

        switch (true) {
            case ($obj instanceof PagesNode):
                $this->masterCatalog->addItem('Pages', $obj->getReference());
                break;
        }

        if (method_exists($obj, 'getNameForNamesCatalog')) {
            $this->namesCatalog->addItem($obj->getNameForNamesCatalog(), $obj->getReference());
        }

        return $id;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @throws \Exception
     */
    public function setMetadataInfo($key, $value)
    {
        switch (true){
            case is_string($value):
                $value = new PdfString($value);
                break;

            default:
                throw new \Exception('Type of "'.print_r($value, true).'" not recognized"');
        }
        $this->metadataDict->addItem($key, $value);
    }

    /**
     * @return string
     */
    protected function generateBuffer()
    {
        $buffer = "%PDF-" . self::VERSION . "\r\n";
        $buffer .= "%\xE2\xE3\xCF\xD3\r\n";
        $buffer .= sprintf("%% created on %s\r\n", date('Y-m-d H:i:j'));
        $crossRefTable = new CrossRefTable();

        $this->trailer
            ->setSize(count($this->objects))
            ->setRoot($this->masterCatalog)
            ->setInfo($this->metadataDict)
        ;

        $pending = $this->objects;

        while (count($pending) > 0) {
            foreach ($pending as $index => $object) {
                try {
                    $tmp = $object->dump();
                } catch (ReferenceNotResolved $e) {
                    // do nothing, hope we solve reference in the next loop
                    continue;
                }
                $buffer .= "\r\n";
                $crossRefTable->addItem($object->getId(), strlen($buffer));
                $buffer .= $tmp;
                unset($pending[$index]);
            }
        }

        $buffer .= "\r\n";

        $this->trailer->setStartXref(strlen($buffer));

        $buffer .= $crossRefTable->dump();

        $buffer .= "\r\n".$this->trailer->dump();

        unset($crossRefTable);

        return $buffer;
    }

    /**
     * @param string $file
     * @throws \Exception
     */
    public function saveToFile($file)
    {
        $buffer = $this->generateBuffer();

        if (strpos($file, '://') === false) {
            $file = 'file://'.$file;
        }
        $handle = fopen($file, 'wb');
        if (!$handle) {
            throw new \Exception("Some error creating {$file}");
        }
        fwrite($handle, $buffer, strlen($buffer));
        fclose($handle);
    }
}
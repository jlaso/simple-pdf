<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

class Trailer extends AbstractPart implements PartInterface
{
    const NAME = 'trailer';

    /** @var PdfNumber */
    protected $size;
    /** @var Catalog */
    protected $root;
   // /** @var string */
   // protected $ID;   //  /ID[<E3FEB541622C4F35B45539A690880C71><E3FEB541622C4F35B45539A690880C71>]
    /** @var PdfString */
    protected $encrypt;
    /** @var MetadataDict */
    protected $info;
    /** @var int */
    protected $startXref = 0;

    /**
     * @param Int $size
     * @return Trailer
     */
    public function setSize($size)
    {
        // +1 because the first entry in xref is bulk
        $this->size = new PdfNumber($size+1);

        return $this;
    }

    /**
     * @param Catalog $root
     * @return Trailer
     */
    public function setRoot(Catalog $root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * @param int $startXref
     */
    public function setStartXref($startXref)
    {
        $this->startXref = $startXref;
    }

//    /**
//     * @param string $ID
//     * @return Trailer
//     */
//    public function setID($ID)
//    {
//        $this->ID = $ID;
//
//        return $this;
//    }

    /**
     * @param string $encrypt
     * @return Trailer
     */
    public function setEncrypt($encrypt)
    {
        $this->encrypt = new PdfString($encrypt);

        return $this;
    }

    /**
     * @param MetadataDict $info
     * @return Trailer
     */
    public function setInfo(MetadataDict $info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $result = [
            'Size' => $this->size,
            'Root' => $this->root->getReference(),
        ];
//        if ($this->ID) {
//            $result['ID'] = $this->ID;
//        }
        if ($this->encrypt) {
            $result['Encrypt'] = $this->encrypt;
        }
        if ($this->info) {
            $result['Info'] = $this->info->getReference();
        }

        return
            $this->dumpBlock(self::NAME, $result).
            "\r\n\r\nstartxref\r\n".$this->startXref."\r\n%%EOF";
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return 'Trailer '.md5($this->dump());
    }
}
<?php

namespace PHPfriends\SimplePdf\Adaptor;

interface FilterInterface
{
    // page 1000 of pdf_reference_1.7

    const ASCIIHexDecode = 'ASCIIHexDecode';
    const ASCII85Decode = 'ASCII85Decode';
    const LZWDecode = 'LZWDecode';
    const FlateDecode = 'FlateDecode';
    const RunLengthDecode = 'RunLengthDecode';
    const CCITTFaxDecode = 'CCITTFaxDecode';
    const DCTDecode = 'DCTDecode';

    /**
     * @param string $data
     * @return string
     */
    public function filter($data);

    /**
     * @return string
     */
    public function getName();
}
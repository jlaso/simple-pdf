<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

use PHPfriends\SimplePdf\Exceptions\ReferenceNotResolved;

class PageNode extends Dictionary
{
    const TYPE = 'Page';

    use LazyReferenceTrait;

    /** @var PagesNode */
    protected $parent;
    /** @var ResourceNode */
    protected $resources;
    /** @var Box */
    protected $mediaBox;
    /** @var Box */
    protected $cropBox = null;
    /** @var Content */
    protected $contents = null;

    /**
     * @param PagesNode $parent
     * @param ResourceNode $resources
     * @param Box $mediaBox
     */
    public function __construct(
        PagesNode $parent,
        ResourceNode $resources,
        Box $mediaBox
    ) {
        $this->parent = $parent;
        $this->resources = $resources;
        $this->mediaBox = $mediaBox;
        $this->parent->addPage($this);
    }

    /**
     * @param Box $cropBox
     */
    public function setCropBox($cropBox)
    {
        $this->cropBox = $cropBox;
    }

    /**
     * @param Content $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return string
     * @throws ReferenceNotResolved
     */
    public function dump()
    {
        $this->addItem('Parent', $this->parent);
        $this->addItem('MediaBox', $this->mediaBox);
        $this->addItem('Resources', $this->resources);
        if($this->cropBox){
            $this->addItem('CropBox', $this->cropBox);
        }
        if($this->contents){
            $this->addItem('Contents', $this->contents);
        }

        return parent::dump();
    }
}
<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

use PHPfriends\SimplePdf\Exceptions\ReferenceNotResolved;

class PagesNode extends Dictionary
{
    const TYPE = 'Pages';
    const NAME = 'Pages';

    use LazyReferenceTrait;
    use NameTrait;

    /** @var PageNode[] */
    protected $pages;

    public function addPage(PageNode $page)
    {
        $this->pages[] = $page;
    }

    /**
     * @return string
     * @throws ReferenceNotResolved
     */
    public function dump()
    {
        $kids = new PdfArray();
        foreach($this->pages as $page){
            $kids->addItem($page->getReference());
        }
        $this->addItem('Kids', $kids);
        $this->addItem('Count', new PdfInteger(count($this->pages)));

        return parent::dump();
    }

}
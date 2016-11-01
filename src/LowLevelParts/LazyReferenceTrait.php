<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

use PHPfriends\SimplePdf\Exceptions\ReferenceNotResolved;

trait LazyReferenceTrait
{
    /** @var Reference */
    protected $reference = null;

    /**
     * @return Reference
     * @throws ReferenceNotResolved
     */
    public function getReference()
    {
        if(null === $this->reference){
            throw new ReferenceNotResolved();
        }

        return $this->reference;
    }

    /**
     * @param ObjectNode $object
     */
    public function injectObject(ObjectNode $object)
    {
        $this->reference = $object->getReference();
    }

    /**
     * @return bool
     */
    public function isResolved()
    {
        return (bool) $this->reference;
    }
}
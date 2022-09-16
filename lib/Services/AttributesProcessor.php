<?php


namespace SimpleSAML\Module\oauth2\Services;

/**
 * Before being stored, user attributes must be processed.
 * At the moment the only operation is to convert single valued attributes
 * from array of length 1 to their first element
 */
class AttributesProcessor
{
    private $singleValuedAttributes;

    public function __construct($singleValuedAttributes)
    {
        $this->singleValuedAttributes = $singleValuedAttributes ?? [];
    }

    public function processAttributes($attributes)
    {
        $processedAttributes = [];
        foreach ($attributes as $name => $value) {
            $processedAttributes[$name] = $this->isSingleValued($name) ? $value[0] : $value;
        }

        return $processedAttributes;
    }

    private function isSingleValued($attribute)
    {
        return in_array($attribute, $this->singleValuedAttributes, true);
    }
}

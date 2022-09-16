<?php

namespace SimpleSAML\Test\Module\oauth2\Services;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\oauth2\Services\AttributesProcessor;

class AttributesProcessorTest extends TestCase
{
    public function testProcessAttributes()
    {
        $processor = new AttributesProcessor(['single_valued_attribute']);

        $attributes = [
            'single_valued_attribute' => [ 'single' ],
            'attr1' => [ 'one', 'two'],
            'attr2' => [ 'a', 'b'],
        ];

        $expectedProcessedAttributes = [
            'single_valued_attribute' => 'single',
            'attr1' => [ 'one', 'two'],
            'attr2' => [ 'a', 'b'],
        ];
        $processedAttributes = $processor->processAttributes($attributes);

        $this->assertEquals($expectedProcessedAttributes, $processedAttributes);
    }
}
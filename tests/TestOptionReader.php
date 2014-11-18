<?php namespace Indatus\Dispatcher;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use TestCase;

class TestOptionReader extends TestCase
{
    public function testDebugMode()
    {
        $optionReader = new OptionReader([]);
        $this->assertEquals(false, $optionReader->isDebugMode());

        $optionReader = new OptionReader([
            'debug' => true,
        ]);
        $this->assertEquals(true, $optionReader->isDebugMode());
    }
}

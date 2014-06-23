<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\Dispatcher\OptionReader;

class TestOptionReader extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testDebugMode()
    {
        $optionReader = new OptionReader(array());
        $this->assertEquals(false, $optionReader->isDebugMode());

        $optionReader = new OptionReader(array(
            'debug' => true
        ));
        $this->assertEquals(true, $optionReader->isDebugMode());
    }
} 
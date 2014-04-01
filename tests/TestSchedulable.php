<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\Dispatcher\BackgroundProcess;

class TestSchedulable extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testArguments()
    {
        $args = ['type' => 'scheduled'];
        /** @var \Indatus\Dispatcher\Schedulable $scheduleable */
        $scheduleable = $this->getMockForAbstractClass(
            'Indatus\Dispatcher\Schedulable', array(
                App::make('Indatus\Dispatcher\ConfigResolver')
            ));
        $newScheduleable = $scheduleable->args($args);
        $this->assertEquals($args, $newScheduleable->getArguments());
    }

    public function testArgumentsConstructor()
    {
        $args = ['type' => 'scheduled'];

        /** @var \Indatus\Dispatcher\Schedulable $scheduleable */
        $scheduleable = $this->getMockForAbstractClass(
            'Indatus\Dispatcher\Schedulable', array(
                App::make('Indatus\Dispatcher\ConfigResolver'),
                $args
            )
        );

        $this->assertEquals($args, $scheduleable->getArguments());
    }
} 
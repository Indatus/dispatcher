<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\Dispatcher\BackgroundProcess;
use Indatus\Dispatcher\ServiceProvider;
use Mockery as m;

class TestServiceProvider extends TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testProviders()
    {
        $provider = new ServiceProvider(m::mock('Illuminate\Foundation\Application'));

        $this->assertCount(3, $provider->provides());

        $this->assertContains('command.scheduled.summary', $provider->provides());
        $this->assertContains('command.scheduled.make', $provider->provides());
        $this->assertContains('command.scheduled.run', $provider->provides());
    }
} 
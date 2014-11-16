<?php namespace Indatus\Dispatcher;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use TestCase;

class TestServiceProvider extends TestCase
{
    public function testProviders()
    {
        $provider = new ServiceProvider(m::mock('Illuminate\Foundation\Application'));

        $this->assertCount(3, $provider->provides());

        $this->assertContains('command.scheduled.summary', $provider->provides());
        $this->assertContains('command.scheduled.make', $provider->provides());
        $this->assertContains('command.scheduled.run', $provider->provides());
    }
}

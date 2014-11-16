<?php namespace Indatus\Dispatcher\Services;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use TestCase;

class TestBackgroundProcessService extends TestCase
{
    /** @var BackgroundProcessService */
    private $backgroundProcessService;

    /** @var \Indatus\Dispatcher\Platform */
    private $platform;

    public function setUp()
    {
        parent::setUp();

        $this->backgroundProcessService = new BackgroundProcessService();

        $this->platform = m::mock('Indatus\Dispatcher\Platform');
        $this->app->instance('Indatus\Dispatcher\Platform', $this->platform);
    }

    public function testCantRunAsUserOnLinux()
    {
        $this->backgroundProcessService = m::mock('Indatus\Dispatcher\Services\BackgroundProcessService[isRoot]');

        $this->platform->shouldReceive('isWindows')->andReturn(false);
        $this->backgroundProcessService->shouldReceive('isRoot')->andReturn(true);

        $this->assertTrue($this->backgroundProcessService->canRunAsUser());
    }

    public function testCanRunAsUser()
    {
        $this->platform->shouldReceive('isWindows')->andReturn(true);

        $this->assertFalse($this->backgroundProcessService->canRunAsUser());
    }

    public function testIsRoot()
    {
        $this->assertFalse($this->backgroundProcessService->isRoot());
    }
}

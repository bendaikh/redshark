<?php

namespace Tests;

use App\Services\LicenseService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Feature tests exercise HTTP routes. The license gate would otherwise
        // redirect every request to activation when no license is configured.
        $this->mock(LicenseService::class, function ($mock) {
            $mock->shouldReceive('isValid')->andReturn(true);
        });
    }
}

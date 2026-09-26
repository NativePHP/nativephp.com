<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Ai\Classification;

abstract class TestCase extends BaseTestCase
{
    /**
     * Jev is a paid API that queued jobs reach synchronously in tests, so it's
     * always faked. Tests that care what it answers fake it again themselves.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Classification::fake();
    }
}

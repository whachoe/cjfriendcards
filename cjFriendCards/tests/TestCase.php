<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use \Tests\CreatesApplication;

    // use RefreshDatabase in tests individually as needed
}

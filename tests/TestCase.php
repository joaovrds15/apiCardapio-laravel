<?php

namespace Tests;

use App\Http\Middleware\JWTVerification;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    
}

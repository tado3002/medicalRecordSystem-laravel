<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from appointments');
        DB::delete('delete from patients');
        DB::delete('delete from docters');
        DB::delete('delete from personal_access_tokens');
        DB::delete('delete from users');
    }
}

<?php

namespace Tests;

use Helldar\LaravelActions\ServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Concerns\Actionable;
use Tests\Concerns\Database;
use Tests\Concerns\Files;
use Tests\Concerns\Settings;
use Tests\Concerns\Sleepable;

abstract class TestCase extends BaseTestCase
{
    use Actionable;
    use Database;
    use Files;
    use RefreshDatabase;
    use Settings;
    use Sleepable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->freshDatabase();
        $this->freshFiles();
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->setTable($app);
        $this->setDatabase($app);
    }

    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }

    protected function table()
    {
        return DB::table($this->table);
    }
}
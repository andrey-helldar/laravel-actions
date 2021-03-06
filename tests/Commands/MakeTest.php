<?php

namespace Tests\Commands;

use Tests\TestCase;

final class MakeTest extends TestCase
{
    public function testMakingFiles()
    {
        $name = 'MakeExample';

        $filename = date('Y_m_d_His') . '_make_example.php';

        $path = database_path('actions/' . $filename);

        $this->assertFileDoesNotExist($path);

        $this->artisan('make:migration:action', compact('name'))->run();

        $this->assertFileExists($path);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../fixtures/stubs/make_example.stub'),
            file_get_contents($path)
        );
    }
}

<?php

namespace Helldar\LaravelActions\Support;

use Helldar\LaravelActions\Traits\Infoable;
use Illuminate\Database\Migrations\Migrator as BaseMigrator;

final class Migrator extends BaseMigrator
{
    use Infoable;

    public function usingConnection($name, callable $callback)
    {
        $prev = $this->resolver->getDefaultConnection();

        $this->setConnection($name);

        return tap($callback(), function () use ($prev) {
            $this->setConnection($prev);
        });
    }
}

<?php

namespace Helldar\LaravelActions\Traits;

/** @mixin \Illuminate\Console\Command */
trait Argumentable
{
    protected function argumentName(): string
    {
        $value = (string) $this->argument('name');

        return trim($value);
    }
}

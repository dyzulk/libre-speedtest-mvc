<?php

namespace App\Core;

interface Middleware
{
    /**
     * Handle the incoming request.
     *
     * @return void
     */
    public function handle(): void;
}

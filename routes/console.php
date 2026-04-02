<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('app:ping', function () {
    $this->comment('pong');
});

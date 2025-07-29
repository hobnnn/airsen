<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HelloWorldCommand extends Command
{
    protected $signature = 'demo:hello';
    protected $description = 'Outputs Hello World to the console';

    public function handle()
{
    $logs = \Cache::get('hello_world_logs', []);
    $logs[] = 'Hello World fired at ' . now();
    \Cache::put('hello_world_logs', $logs, now()->addMinutes(1));
}
    
}
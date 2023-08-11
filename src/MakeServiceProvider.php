<?php

namespace Codewiser\Make;

use Codewiser\Make\Console\Commands\BuilderMakeCommand;
use Codewiser\Make\Console\Commands\CollectionMakeCommand;
use Illuminate\Support\ServiceProvider;

class MakeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerCommands();
    }

    /**
     * Register the package's commands.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {

            $commands = [
                BuilderMakeCommand::class,
                CollectionMakeCommand::class,
            ];

            $this->commands($commands);
        }
    }
}
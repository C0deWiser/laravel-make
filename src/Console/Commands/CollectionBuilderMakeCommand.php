<?php

namespace Codewiser\Make\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:bc')]
class CollectionBuilderMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:bc {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Model Collection and Eloquent builder class';

    public function handle(): void
    {
        $model = $this->argument('model');
        $collection = $model.'Collection';
        $ns_collection = '\\App\\Collections\\'.$collection;

        $this->call('make:collection', [
            'name' => $collection,
            '--model' => $model,
        ]);

        $builder = $model.'Builder';
        $ns_builder = '\\App\\Builders\\'.$builder;

        $this->call('make:builder', [
            'name' => $builder,
            '--model' => $model,
        ]);

        $this->info('Modify your '.$model);
        $this->line('/**');
        $this->line(' * @method static '.$ns_builder.' query()');
        $this->line(' * @method static '.$ns_collection.' all($columns = [\'*\'])');
        $this->line(' */');
        $this->line('class '.$model);
        $this->line('{');
        $this->line('   public function newCollection(array $models = []): '.$ns_collection);
        $this->line('   {');
        $this->line('       return new '.$ns_collection.'($models);');
        $this->line('   }');
        $this->line('');
        $this->line('   public function newEloquentBuilder($query): '.$ns_builder);
        $this->line('   {');
        $this->line('       return new '.$ns_builder.'($query);');
        $this->line('   }');
        $this->line('}');
    }
}
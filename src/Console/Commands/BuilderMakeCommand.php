<?php

namespace Codewiser\Make\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:builder')]
class BuilderMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:builder {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent builder class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Builder';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/builder.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     *
     * @return string
     */
    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Builders';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     *
     * @return string
     * @throws FileNotFoundException
     */
    protected function buildClass($name): string
    {
        $stub = $this->replaceUserNamespace(
            parent::buildClass($name)
        );

        $model = $this->option('model')
            ? $this->qualifyModel($this->option('model'))
            : $this->qualifyModel($this->guessModelName($name));

        $collection = $this->option('model')
            ? $this->qualifyCollection($this->option('model'))
            : $this->qualifyCollection($this->guessCollectionName($name));

        return $model ? $this->replaceModel($stub, $model, $collection) : $stub;
    }

    /**
     * Guess the model name from the Factory name or return a default model name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function guessModelName(string $name): string
    {
        if (str_ends_with($name, 'Builder')) {
            $name = str($name)->substr(0, -7)->afterLast('\\');
        }

        $modelName = $this->qualifyModel(str($name)->after($this->rootNamespace()));

        if (class_exists($modelName)) {
            return $modelName;
        }

        if (is_dir(app_path('Models/'))) {
            return $this->rootNamespace().'Models\Model';
        }

        return $this->rootNamespace().'Model';
    }

    /**
     * Guess the model collection name from the Factory name or return a default collection name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function guessCollectionName(string $name): string
    {
        if (str_ends_with($name, 'Builder')) {
            $name = str($name)->substr(0, -7)->afterLast('\\');
        }

        $name = $name.'Collection';

        $collectionName = $this->qualifyCollection(str($name)->after($this->rootNamespace()));

        if (class_exists($collectionName)) {
            return $collectionName;
        }

        return 'Illuminate\Database\Eloquent\Collection';
    }

    /**
     * Qualify the given collection class base name.
     *
     * @param  string  $collection
     *
     * @return string
     */
    protected function qualifyCollection(string $collection)
    {
        $collection = ltrim($collection, '\\/');

        $collection = str_replace('/', '\\', $collection);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($collection, $rootNamespace)) {
            return $collection;
        }

        if ($collection === Collection::class) {
            return $collection;
        }

        return is_dir(app_path('Collections'))
            ? $rootNamespace.'Collections\\'.$collection
            : $rootNamespace.$collection;
    }

    /**
     * Replace the User model namespace.
     *
     * @param  string  $stub
     *
     * @return string
     */
    protected function replaceUserNamespace(string $stub): string
    {
        $model = $this->userProviderModel();

        if (!$model) {
            return $stub;
        }

        return str_replace(
            $this->rootNamespace().'User',
            $model,
            $stub
        );
    }

    /**
     * Replace the model for the given stub.
     *
     * @param  string  $stub
     * @param  string  $model
     *
     * @return string
     */
    protected function replaceModel(string $stub, string $model, string $collection): string
    {
        $model = str_replace('/', '\\', $model);
        $collection = str_replace('/', '\\', $collection);

        if (str_starts_with($model, '\\')) {
            $namespacedModel = trim($model, '\\');
        } else {
            $namespacedModel = $this->qualifyModel($model);
        }

        if (str_starts_with($collection, '\\')) {
            $namespacedCollection = trim($collection, '\\');
        } else {
            $namespacedCollection = $this->qualifyCollection($collection);
        }

        $model = class_basename(trim($model, '\\'));
        $collection = class_basename(trim($collection, '\\'));

        $dummyUser = class_basename($this->userProviderModel());

        $dummyModel = Str::camel($model) === 'user' ? 'model' : $model;

        $replace = [
            'NamespacedDummyModel'       => $namespacedModel,
            '{{ namespacedModel }}'      => $namespacedModel,
            '{{namespacedModel}}'        => $namespacedModel,
            '{{ namespacedCollection }}' => $namespacedCollection,
            '{{namespacedCollection}}'   => $namespacedCollection,
            'DummyModel'                 => $model,
            '{{ model }}'                => $model,
            '{{model}}'                  => $model,
            'dummyModel'                 => Str::camel($dummyModel),
            '{{ modelVariable }}'        => Str::camel($dummyModel),
            '{{modelVariable}}'          => Str::camel($dummyModel),
            'DummyUser'                  => $dummyUser,
            '{{ user }}'                 => $dummyUser,
            '{{user}}'                   => $dummyUser,
            '$user'                      => '$'.Str::camel($dummyUser),
        ];

        $stub = str_replace(
            array_keys($replace), array_values($replace), $stub
        );

        return preg_replace(
            vsprintf('/use %s;[\r\n]+use %s;/', [
                preg_quote($namespacedModel, '/'),
                preg_quote($namespacedModel, '/'),
            ]),
            "use $namespacedModel;",
            $stub
        );
    }

}

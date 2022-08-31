<?php

namespace App\Console\Make;

use Illuminate\Routing\Console\ControllerMakeCommand as BaseControllerMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends BaseControllerMakeCommand
{

    protected string $namespace = '\Http\Controllers';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        if ($this->option('api')) {
            $api_versions = config('api.versions', ['v1']);
            $prefix = config('api.namespace_prefix');
            $this->namespace = $this->namespace . ($prefix === '' ? '' : '\\' . $prefix) . '\\' . end($api_versions);
        }

        parent::handle();

        if ($this->option('api')) {
            $name = str_replace('Controller', '', $this->getNameInput());
            $prefix = $this->getPrefix($name);

            $this->call('make:resource', [
                'name' => $prefix.$name.'Resource',
            ]);

            $this->call('make:resource', [
                'name' => $prefix.$name.'Collection',
            ]);
        }
    }

    protected function getPrefix($name): string
    {
        $prefix = '';

        if ($this->option('api')) {
            $api_versions = config('api.versions', ['v1']);
            $prefix = config('api.namespace_prefix');

            $prefix = ($prefix === '' ? '' : $prefix . '/').end($api_versions).'/'.$name.'/';
        }

        return $prefix;
    }

    /**
     * Generate the form requests for the given model and classes.
     *
     * @param  string  $modelClass
     * @param  string  $storeRequestClass
     * @param  string  $updateRequestClass
     * @return array
     */
    protected function generateFormRequests($modelClass, $storeRequestClass, $updateRequestClass)
    {
        $prefix = $this->getPrefix(class_basename($modelClass));

        $storeRequestClass = $prefix.'Store'.class_basename($modelClass).'Request';

        $this->call('make:request', [
            'name' => $storeRequestClass,
        ]);

        $updateRequestClass = $prefix.'Update'.class_basename($modelClass).'Request';

        $this->call('make:request', [
            'name' => $updateRequestClass,
        ]);

        return [$storeRequestClass, $updateRequestClass];
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.$this->namespace;
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options[] = ['api', null, InputOption::VALUE_NONE, 'Exclude the create and edit methods from the controller and use custom API namespacing.'];

        return $options;
    }
}

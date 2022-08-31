<?php

namespace App\Console\Make;

use Illuminate\Foundation\Console\ResourceMakeCommand as BaseControllerMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class ResourceMakeCommand extends BaseControllerMakeCommand
{
    protected string $namespace = '\Http\Resources';

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

        $options[] = ['api', null, InputOption::VALUE_NONE, 'Use API namespacing for the new request'];

        return $options;
    }
}

<?php

namespace App\Console\Make;

use Illuminate\Foundation\Console\RequestMakeCommand as BaseRequestMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class RequestMakeCommand extends BaseRequestMakeCommand
{
    protected string $namespace = '\Http\Requests';

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
        $options[] = ['group', null, InputOption::VALUE_NONE, 'Group requests from the same controller together in a separate folder'];

        return $options;
    }
}

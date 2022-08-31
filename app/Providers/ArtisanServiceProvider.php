<?php

namespace App\Providers;

use App\Console\Make\ControllerMakeCommand;
use App\Console\Make\RequestMakeCommand;
use App\Console\Make\ResourceMakeCommand;
use Illuminate\Foundation\Providers\ArtisanServiceProvider as BaseArtisanServiceProvider;

class ArtisanServiceProvider extends BaseArtisanServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->devCommands['ControllerMake'] = ControllerMakeCommand::class;
        $this->devCommands['RequestMake'] = RequestMakeCommand::class;
        $this->devCommands['ResourceMake'] = ResourceMakeCommand::class;

        parent::register();
    }
}

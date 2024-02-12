<?php

namespace Laravel\Pulse;

/**
 * @internal
 */
class PulseHeadlessServiceProvider extends PulseServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        if ($this->app->make('config')->get('pulse.enabled')) {
            $this->app->make(Pulse::class)->register($this->app->make('config')->get('pulse.recorders'));
            $this->listenForEvents();
        } else {
            $this->app->make(Pulse::class)->stopRecording();
        }

        $this->registerComponents();
        $this->registerPublishing();
        $this->registerCommands();
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/pulse.php' => config_path('pulse.php'),
            ], ['pulse', 'pulse-config']);

            $method = method_exists($this, 'publishesMigrations') ? 'publishesMigrations' : 'publishes';

            $this->{$method}([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], ['pulse', 'pulse-migrations']);
        }
    }
}

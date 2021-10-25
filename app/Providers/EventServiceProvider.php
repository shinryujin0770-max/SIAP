<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            $event->menu->add([
                'text'        => 'Dashboard',
                'url'         => 'dashboard',
                'icon'        => 'fas fa-fw fa-tachometer-alt',
            ]);
            $event->menu->add('MENU UTAMA');
            $event->menu->add([
                'key'         => 'situ',
                'text'        => 'SITU',
                'url'         => 'situ',
                'icon'        => 'fas fa-fw fa-book',
                'active'      => ['situ', 'situ' . '*', 'regex:@^' . 'situ' . '/[a-zA-Z]/[0-9]+$@'],
            ]);
            $event->menu->addIn('situ', [
                'text'        => 'SITU',
                'url'         => 'situ',
                'icon'        => 'fas fa-fw fa-paste',
                'active'      => ['situ', 'regex:@^' . 'situ' . '/[0-9]+$@'],
            ]);
            $event->menu->addIn('situ', [
                'text'        => 'Registrasi Ulang',
                'url'         => 'situ_reregistration',
                'icon'        => 'fas fa-fw fa-retweet',
                'active'      => ['situ_reregistration', 'situ_reregistration' . '*', 'regex:@^' . 'situ_reregistration' . '/[a-zA-Z]/[0-9]+$@'],
            ]);
            $event->menu->add([
                'text'        => 'Pedagang',
                'url'         => 'pedagang',
                'icon'        => 'fas fa-fw fa-person-booth',
            ]);
            $event->menu->add('PENGATURAN');
            $event->menu->add([
                'text'        => 'Pengguna',
                'url'         => 'users',
                'icon'        => 'fas fa-fw fa-users',
            ]);
            $event->menu->add([
                'key' => 'data_master',
                'text'        => 'Data Master',
                'icon'        => 'fas fa-fw fa-archive',
            ]);
            $event->menu->addIn('data_master', [
                'text'        => 'Jenis Dagangan',
                'url'         => 'jenis_dagangan',
                'icon'        => 'fas fa-fw fa-boxes',
            ]);
            $event->menu->addIn('data_master', [
                'text'        => 'Pasar',
                'url'         => 'pasar',
                'icon'        => 'fas fa-fw fa-store',
            ]);
            $event->menu->addIn('data_master', [
                'text'        => 'Kios',
                'url'         => 'kios',
                'icon'        => 'fas fa-fw fa-cash-register',
            ]);
        });
    }
}

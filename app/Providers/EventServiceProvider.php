<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
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
            $user = User::find(Auth::id());
            $event->menu->add([
                'text'        => 'Dashboard',
                'url'         => 'dashboard',
                'icon'        => 'fas fa-fw fa-tachometer-alt',
            ]);
            $event->menu->add('MENU UTAMA');
            if ($user->hasAnyPermission(['project-read'])) {
                $event->menu->add([
                    'key'         => 'project',
                    'text'        => 'Project',
                    'url'         => 'project',
                    'icon'        => 'fas fa-fw fa-book',
                    'active'      => ['project', 'project' . '*', 'regex:@^' . 'project' . '/[a-zA-Z]/[0-9]+$@'],
                ]);
            }
            if ($user->can('pengaturan-all')) {
                $event->menu->add('PENGATURAN');
                $event->menu->add([
                    'text'        => 'Pengguna',
                    'url'         => 'user',
                    'icon'        => 'fas fa-fw fa-users',
                ]);
            }
        });
    }
}

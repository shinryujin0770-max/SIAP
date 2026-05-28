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
            if ($user->hasAnyRole(['admin_opd'])) {
                $event->menu->add([
                    'key'         => 'project',
                    'text'        => 'Pekerjaan Konstruksi',
                    'icon'        => 'fas fa-fw fa-briefcase',
                    'active'      => ['project', 'project' . '*', 'regex:@^' . 'project' . '/[a-zA-Z]/[0-9]+$@'],
                ]);
                $event->menu->addIn('project', [
                    'text'        => 'Paket Pekerjaan Konstruksi',
                    'url'         => 'project',
                    'icon'        => 'fas fa-fw fa-briefcase',
                    'active'      => ['project', 'project' . '*', 'regex:@^' . 'project' . '/[a-zA-Z]/[0-9]+$@'],
                    'parent'      => 'project',
                ]);
            }
            if ($user->hasAnyRole(['superadmin'])) {
                $event->menu->add([
                    'key'         => 'project',
                    'text'        => 'Pekerjaan Konstruksi',
                    'icon'        => 'fas fa-fw fa-briefcase',
                    'active'      => ['project', 'project' . '*', 'regex:@^' . 'project' . '/[a-zA-Z]/[0-9]+$@'],
                ]);
                $event->menu->addIn('project', [
                    'text'        => 'Paket Pekerjaan Konstruksi',
                    'url'         => 'project',
                    'icon'        => 'fas fa-fw fa-briefcase',
                    'active'      => ['project', 'project' . '*', 'regex:@^' . 'project' . '/[a-zA-Z]/[0-9]+$@'],
                    'parent'      => 'project',
                ]);
                $event->menu->addIn('project', [
                    'key'         => 'payment',
                    'text'        => 'Pembayaran',
                    'url'         => 'payment',
                    'icon'        => 'fas fa-fw fa-money-check',
                    'active'      => ['payment', 'payment' . '*', 'regex:@^' . 'payment' . '/[a-zA-Z]/[0-9]+$@'],
                ]);
                $event->menu->addIn('project', [
                    'key'         => 'report',
                    'text'        => 'Pelaporan',
                    'url'         => 'report',
                    'icon'        => 'fas fa-fw fa-book',
                    'active'      => ['report', 'report' . '*', 'regex:@^' . 'report' . '/[a-zA-Z]/[0-9]+$@'],
                ]);
            }
            // if ($user->hasAnyRole(['admin_rfk', 'superadmin'])) {
            //     $event->menu->add([
            //         'key'         => 'rfk',
            //         'text'        => 'RFK',
            //         'url'         => 'rfk',
            //         'icon'        => 'fas fa-fw fa-briefcase',
            //         'active'      => ['rfk', 'rfk' . '*', 'regex:@^' . 'rfk' . '/[a-zA-Z]/[0-9]+$@'],
            //     ]);
            // }
            if ($user->hasAnyRole(['admin_rfk', 'superadmin'])) {
                $event->menu->add([
                    'key'         => 'rfk-v2',
                    'text'        => 'RFK',
                    'url'         => 'rfk-v2',
                    'icon'        => 'fas fa-fw fa-briefcase',
                    'active'      => ['rfk-v2', 'rfk-v2' . '*', 'regex:@^' . 'rfk-v2' . '/[a-zA-Z]/[0-9]+$@'],
                ]);
            }
            if ($user->hasRole('superadmin')) {
                $event->menu->add('PENGATURAN');
                $event->menu->add([
                    'text'        => 'Pengguna',
                    'url'         => 'user',
                    'icon'        => 'fas fa-fw fa-users',
                ]);
                $event->menu->add([
                    'text'        => 'OPD',
                    'url'         => 'opd',
                    'icon'        => 'fas fa-fw fa-map',
                ]);
            }
        });
    }
}

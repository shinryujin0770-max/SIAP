<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use Notifiable, HasMediaTrait, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'id_opd'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function adminlte_image()
    {
        return env('APP_URL') . '/user.png';
    }

    public function adminlte_name()
    {
        $user = AuthUser::find(Auth::id());
        $res = 'Superadmin';
        if ($user) {
            $res = $user['name'];
        }
        return $res;
    }

    public function adminlte_desc()
    {
        $user = AuthUser::find(Auth::id());
        $res = 'Superadmin';
        if ($user) {
            $res = $user['name'];
        }
        return $res;
    }

    public function adminlte_profile_url()
    {
        return 'profile';
    }
}

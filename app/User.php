<?php

namespace App;

use App\Models\Building;
use App\Models\BuildingList;
use App\Models\Resource;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'nickname', 'kingdom', 'capital'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function resource() {
        return $this->hasOne(Resource::class, 'userId', 'id');
    }

    public function building() {
        return $this->hasOne(Building::class, 'userId', 'id');
    }

    public function buildingList() {
        return $this->hasMany(BuildingList::class, 'userId', 'id');
    }

    /**
     * 获取用户 ID
     *
     * @return int 未登录为 0
     */
    static function getUserId()
    {
        if (Auth::check()) {
            return Auth::id();
        }

        return 0;
    }
}

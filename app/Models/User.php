<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Annotations as OA;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * @OA\Schema(
     *     schema="User",
     *     description="user",
     *     @OA\Property(type="integer", property="id"),
     *     @OA\Property(type="string", property="name"),
     *     @OA\Property(type="string", property="password"),
     *     @OA\Property(type="string", format="date-time", property="email_verified_at"),
     *     @OA\Property(type="string", property="remember_token"),
     *     @OA\Property(type="string", format="date-time", property="created_at"),
     *     @OA\Property(type="string", format="date-time", property="updated_at")
     * )
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

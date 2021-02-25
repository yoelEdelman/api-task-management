<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

class Task extends Model
{
    use HasFactory;

    /**
     * @OA\Schema(
     *     schema="Task",
     *     description="task",
     *     @OA\Property(type="integer", property="id", default="1"),
     *     @OA\Property(type="integer", property="user_id", default="1"),
     *     @OA\Property(type="string", property="body", default="some text"),
     *     @OA\Property(type="boolean", property="completed", default="0"),
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
        'body',
        'user_id',
        'completed'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

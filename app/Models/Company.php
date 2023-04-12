<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'title', 'phone', 'description'
    ];

    protected $hidden = ['user_id', 'created_at', 'updated_at'];
}

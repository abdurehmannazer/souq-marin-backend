<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'price', 'city', 'categories', 'files', 'user_id'
    ];

    protected $casts = [
        'categories' => 'array',
        'files' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

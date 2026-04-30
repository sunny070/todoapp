<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'priority',
        'due_date',
        'category',
        'description',
        'order',
        'completed'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'date',
        'order' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

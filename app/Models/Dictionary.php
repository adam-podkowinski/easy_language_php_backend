<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    use HasFactory;

    protected $fillable = ['language', 'user_id', 'flashcard_id'];

    public function words()
    {
        return $this->hasMany(Word::class)->orderBy('created_at', 'desc');
    }
}

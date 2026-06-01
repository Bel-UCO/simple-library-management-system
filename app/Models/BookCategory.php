<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model
{
    protected $fillable = [
        'name',
    ];

    public function bookMetadata() {
        return $this->hasMany(BookMetadata::class);
    }

}

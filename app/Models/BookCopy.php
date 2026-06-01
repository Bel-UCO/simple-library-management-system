<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    protected $fillable = [
        'title',
        'book_metadata_id',
        'status'
    ];

    public function bookMetadata() {
        return $this->belongsTo(BookMetadata::class);
    }

    public function borrowedLogs() {
        return $this->hasMany(BorrowedLog::class);
    }
}

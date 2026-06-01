<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowedLog extends Model
{
    protected $fillable = [
        'user_id',
        'book_copy_id',
        'borrowed_date',
        'due_date',
        'returned_date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function bookCopy() {
        return $this->belongsTo(BookCopy::class);
    }
}

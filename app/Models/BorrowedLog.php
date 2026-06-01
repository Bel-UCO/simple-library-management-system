<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowedLog extends Model
{
    protected $fillable = [
        'user_id',
        'book_copy_id',
        'date_borrowed',
        'due_date',
        'date_returned',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function bookCopy() {
        return $this->belongsTo(BookCopy::class);
    }
}

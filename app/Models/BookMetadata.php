<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookMetadata extends Model
{
    protected $table = 'book_metadata';

    protected $fillable = [
        'title',
        'author',
        'publisher',
        'year_published',
        'isbn',
        'image',
        'language',
        'book_category_id',
        'description'
    ];

    public function bookCategory()
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function bookCopies()
    {
        return $this->hasMany(BookCopy::class, 'book_metadata_id');
    }
}
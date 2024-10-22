<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'title',
        'description',
        'author',
        'publication_year'
    ];

    public function rentedBy()
    {
        return $this->belongsTo(Customer::class, 'rented_by');
    }

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'rented_by.first_name' => $this->rentedBy?->first_name,
            'rented_by.last_name' => $this->rentedBy?->last_name,
        ];
    }
}

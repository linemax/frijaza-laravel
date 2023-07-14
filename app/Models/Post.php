<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'title',
        'introduction',
        'body',
        'read_time',
        'author_id',
        'category_id',
    ];
    public function photo()
    {
        return $this->morphOne(Photo::class, 'photoable');
    }
    public function Author()
    {
        return $this->belongsTo(Author::class);
    }

    public function Category()
    {
        return $this->belongsTo(Category::class);
    }


    public function getAllowedRelationships()
    {
        return ['author','author.photo', 'photo', 'category'];
    }

    public function scopeWithAllowedRelationships($query, $relationships)
    {
        if (!$relationships) {
            return $query;
        }

        $allowedRelationships = $this->getAllowedRelationships();
        $requestedRelationships = preg_split('/[\s,]+/', $relationships);

        $relationshipsToLoad = collect($requestedRelationships)
            ->map(function ($relationship) use ($allowedRelationships) {
                return in_array($relationship, $allowedRelationships) ? $relationship : null;
            })
            ->filter()
            ->toArray();

        $query = $query->with($relationshipsToLoad);

        return $query;
    }

}
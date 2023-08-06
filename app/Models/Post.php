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

    protected $cast = [
        'publish'
    ];
    public function photo()
    {
        return $this->morphOne(Photo::class, 'photoable');
    }
    public function Author()
    {
        return $this->belongsTo(Author::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'post_categories');
    }


    public function getAllowedRelationships()
    {
        return ['author','author.photo', 'photo', 'categories'];
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

    public function broadcastOn(): array
    {
        return [
            $this, $this->photo
        ];
    }

}
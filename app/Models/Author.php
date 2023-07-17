<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Author extends Model implements Searchable
{


    use HasFactory, Uuids;
    public function getSearchResult(): SearchResult
    {
        $url = route('authors.show', $this->id);

        return new SearchResult(
            $this,
            $this->name,
            $url
        );
    }

    protected $fillable = [
        'name',
        'email',
        'phone',
        'bio',
        'user_id',
    ];

    public function photo()
    {
        return $this->morphOne(Photo::class, 'photoable');
    }
    public function Posts()
    {
        return $this->hasMany(Post::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getAllowedRelationships()
    {
        return ['posts','posts.photo','posts.category', 'photo', 'user'];
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

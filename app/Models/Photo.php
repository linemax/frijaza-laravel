<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory, Uuids;


    protected $fillable = [
        'url',
    ];

    protected $hidden = ['photoable_id', 'photoable_type'];

    public function photoable()
    {
        return $this->morphTo(Photo::class);
    }

    public function getAllowedRelationships()
    {
        return ['authors', 'posts', 'services'];
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
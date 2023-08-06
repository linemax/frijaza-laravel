<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Uuids;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];



    public function author()
    {
        return $this->hasOne(Author::class);
    }
    public function getAllowedRelationships()
    {
        return [ 'author', 'author.posts', 'author.photo'];
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
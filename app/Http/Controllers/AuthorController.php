<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Search;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $size = 10;
        $request->validate([
            'size' => ['integer'],
        ]);
        if ($request->has('size')) {
            if ($request->integer('size') === -1) {
                $size = Author::all()->count();
            } else {
                $size = $request->size;
            }
        }
        return Author::orderBy('created_at', 'desc')->withAllowedRelationships($request->query('with'))->paginate($size);
    }


    public function search(Request $request)
    {
        $request->validate([
            'search' => ['string', 'required']
        ]);
        return ((new Search())
            ->registerModel(Author::class, function (ModelSearchAspect $modelSearchAspect) use ($request) {
                $modelSearchAspect
                    ->addSearchableAttribute('name')
                    ->addSearchableAttribute('email')
                    ->addSearchableAttribute('phone')
                    ->addSearchableAttribute('bio')
                    ->addExactSearchableAttribute('id')
                    ->addExactSearchableAttribute('user_id')
                    ->withAllowedRelationships($request->query('with'));
            })->search($request->string('search')))->toArray();
    }


    public function photo(StorePhotoRequest $request, Author $author)
    {
        $path = $request->photo->store('photos', 'public');
        if ($author->photo()->exists()) {
            Storage::delete($author->photo->url);
            $author->photo->url = $path;
            $author->photo->save();
        } else {
            $photo = new Photo;
            $photo->url = $path;
            $photo->save();
            $photo->refresh();
            $author->photo()->save($photo);
        }
        return response(status:204);
    }



    public function getPhoto(Author $author)
    {
        return $author->photo();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request)
    {
        Author::create($request->validated());
        return response(status: 204);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author, Request $request)
    {

        return Author::where('id', $author->id)->withAllowedRelationships($request->query('with'))->first();
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, Author $author)
    {
        $author->update($request->validated());
        return response(status: 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        $author->delete();
        return response(status: 204);
    }
}
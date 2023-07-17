<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Photo;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
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
                $size = Post::all()->count();
            } else {
                $size = $request->size;
            }
        }
        return Post::orderBy('created_at', 'desc')->withAllowedRelationships($request->query('with'))->paginate($size);
    }

    public function getLatestPost(Post $post, Request $request)
    {

        return Post::where('id', $post->id)->orderBy('created_at', 'desc')->withAllowedRelationships($request->query('with'))->get()->first();
    }


    public function photo(StorePhotoRequest $request, Post $post)
    {
        $path = $request->photo->store('photos', 'public');
        if ($post->photo()->exists()) {
            Storage::delete($post->photo->url);
            $post->photo->url = $path;
            $post->photo->save();
        } else {
            $photo = new Photo;
            $photo->url = $path;
            $photo->save();
            $photo->refresh();
            $post->photo()->save($photo);
        }
        return response(status: 204);
    }



    public function getPhoto(Post $post)
    {
        return $post->photo();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->validated());
        if ($request->has('categories')){

            $post->categories()->attach($request->categories);
        }
        return response(status: 204);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post, Request $request)
    {
        return Post::where('id', $post->id)->withAllowedRelationships($request->query('with'))->first();
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());
        return response(status: 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response(status: 204);
    }
}
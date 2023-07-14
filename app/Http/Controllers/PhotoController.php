<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;
use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */ public function index()
    {
        //
        return Photo::all();
    }



    public function serve(Photo $photo)
    {
        return response()->file('storage/'.$photo->url, );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePhotoRequest $request)
    {
        Photo::create([
            'url'=>$request->string('url'),
            'photoable_id'=>$request->string('photoable_id'),
            'photoable_type'=>$request->string('photoable_type'),
        ]);
        return response(status:200);
    }



    public function deleteMultiple(Request $request)
    {
        //
        $request->validate([
            'photos'=>['required']
        ]);
        foreach ($request->photos as $photo_id) {
            $photo = Photo::findOrFail($photo_id);
            $photo->delete();
        }
        return response(status: 204);
    }

    /**
     * Display the specified resource.
     */
    public function show(Photo $photo, Request $request)
    {
        return Photo::where('id', $photo->id)->withAllowedRelationships($request->query('with'))->first();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePhotoRequest $request, Photo $photo)
    {
        $photo->update($request->validated());
        return response(status:200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Photo $photo)
    {
        $photo->delete();
        return response(status:204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Photo;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $size = 5;
        $request->validate([
            'size' => ['integer'],
        ]);
        if ($request->has('size')) {
            if ($request->integer('size') === -1) {
                $size = Service::all()->count();
            } else {
                $size = $request->size;
            }
        }
        return Service::orderBy('created_at', 'desc')->withAllowedRelationships($request->query('with'))->paginate($size);
    }


    public function photo(StorePhotoRequest $request, Service $service)
    {
        $path = $request->photo->store('photos', 'public');
        if ($service->photo()->exists()) {
            Storage::delete($service->photo->url);
            $service->photo->url = $path;
            $service->photo->save();
        } else {
            $photo = new Photo;
            $photo->url = $path;
            $photo->save();
            $photo->refresh();
            $service->photo()->save($photo);
        }
        return response(status: 201);
    }



    public function getPhoto(Service $service)
    {
        return $service->photo();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        Service::create($request->validated());
        return response(status: 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service, Request $request)
    {
        return Service::where('id', $service->id)->withAllowedRelationships($request->query('with'))->first();
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->validated());
        return response(status: 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return response(status: 204);
    }
}

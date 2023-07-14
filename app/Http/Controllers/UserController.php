<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;
use Spatie\Searchable\ModelSearchAspect;

class UserController extends Controller
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
                $size = User::all()->count();
            } else {
                $size = $request->size;
            }
        }
        return User::paginate($size);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => ['string', 'required']
        ]);
        return ((new Search())
            ->registerModel(User::class, function (ModelSearchAspect $modelSearchAspect) use ($request) {
                $modelSearchAspect
                    ->addSearchableAttribute('name')
                    ->addSearchableAttribute('email')
                    ->addExactSearchableAttribute('id')
                    ->withAllowedRelationships($request->query('with'));
            })->search($request->string('search')))->toArray();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function getCount()
    {
        $deliveriesCount = User::count();

        return response()->json([
            'deliveries_count' => $deliveriesCount,
        ]);
    }

    public function getUsersByDay(Request $request)
    {
        // Validate the dates
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $dates = [];
        $currentDate = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        while ($currentDate->lte($endDateTime)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        $authors = User::selectRaw("DATE(created_at) as date, COUNT(*) as count")
            // ->where([['created_at', '>=', $startDate], ['created_at', '<=', $endDate]])
            ->groupBy('date')
            ->orderBy('date')
            ->get();


        $dateCounts = $authors->pluck('count', 'date');
        $data = [];
        foreach ($dates as $date) {
            $count = $dateCounts->get($date, 0);
            $data[] = $count;
        }

        return response()->json([
            'labels' => $dates,
            'data' => $data,
        ]);
    }
}

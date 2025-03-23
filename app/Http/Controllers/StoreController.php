<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStoreRequest;
use App\Models\PostcodeLocation;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Store::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreRequest $request): JsonResponse
    {
        return response()->json(Store::query()->create($request->validated()), 201);
    }

    public function nearby(Request $request): JsonResponse
    {
        if (!$request->has('postcode')) {
            return response()->json(['error' => 'postcode is required'], 400);
        }

        /** @var PostcodeLocation|null $postcodeLocation */
        $postcodeLocation = PostcodeLocation::query()->find(
            strtoupper(
                str_replace(' ', '',  $request->get('postcode'))
            )
        );

        if (!$postcodeLocation) {
            return response()->json(['error' => 'postcode not found'], 404);
        }

        $stores = Store::queryWithDistance($postcodeLocation->latitude, $postcodeLocation->longitude)
            ->orderBy('distance')
            ->get();

        return response()->json($stores);
    }

    public function deliver(Request $request): JsonResponse
    {
        if (!$request->has('postcode')) {
            return response()->json(['error' => 'postcode is required'], 400);
        }

        /** @var PostcodeLocation|null $postcodeLocation */
        $postcodeLocation = PostcodeLocation::query()->find(
            strtoupper(
                str_replace(' ', '',  $request->get('postcode'))
            )
        );

        if (!$postcodeLocation) {
            return response()->json(['error' => 'postcode not found'], 404);
        }

        return response()->json(
            Store::queryWithDistance($postcodeLocation->latitude, $postcodeLocation->longitude)
                ->having('distance', '<', DB::raw('`stores`.`max_delivery_distance`'))
                ->orderBy('distance')
                ->get()
        );
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataSource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DataSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataSources = auth()->user()->dataSources()->get();
        return response()->json($dataSources);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:mysql,pgsql,sqlite,json,array',
            'configuration' => 'required|array',
        ]);

        $dataSource = auth()->user()->dataSources()->create([
            'name' => $request->name,
            'type' => $request->type,
            'configuration' => $request->configuration,
        ]);

        return response()->json($dataSource, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(DataSource $dataSource)
    {
        if ($dataSource->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }
        return response()->json($dataSource);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataSource $dataSource)
    {
        if ($dataSource->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:mysql,pgsql,sqlite,json,array',
            'configuration' => 'required|array',
        ]);

        $dataSource->update($request->only(['name', 'type', 'configuration']));

        return response()->json($dataSource);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataSource $dataSource)
    {
        if ($dataSource->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $dataSource->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
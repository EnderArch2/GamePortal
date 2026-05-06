<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GameVersion;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    // 1. Get Query Parameters with Defaults
    $page = $request->query('page', 0);
    $size = $request->query('size', 10);
    $sortBy = $request->query('sortBy', 'title');
    $sortDir = $request->query('sortDir', 'asc');

    $query = Game::has('versions')
        ->with(['author', 'versions' => function($q) {
            $q->latest();
        }])
        ->withCount('scores');

    if ($sortBy === 'popular') {
        $query->orderBy('scores_count', $sortDir);
    } elseif ($sortBy === 'uploaddate') {
        $query->orderBy(
            GameVersion::select('created_at')
                ->whereColumn('game_id', 'games.id')
                ->latest()
                ->take(1),
            $sortDir
        );
    } else {
        $query->orderBy('title', $sortDir);
    }

    $gamesPaginated = $query->paginate($size, ['*'], 'page', $page + 1);

    $content = $gamesPaginated->map(function ($game) {
        $latestVersion = $game->versions->first();

        return [
            "slug" => $game->slug,
            "title" => $game->title,
            "description" => $game->description,
            "thumbnail" => $latestVersion->thumbnail_path ?? null, // Note 2
            "uploadTimestamp" => $latestVersion->created_at->toISOString(),
            "author" => $game->author->username,
            "scoreCount" => (int) $game->scores_count
        ];
    });

    return response()->json([
        "page" => (int) $page,
        "size" => (int) $size,
        "totalElements" => $gamesPaginated->total(),
        "content" => $content
    ]);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

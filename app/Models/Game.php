<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Game extends Model
{
    protected $guarded = ['id'];

    public function versions()
    {
        return $this->hasMany(GameVersion::class, 'game_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scores()
    {
        return $this->hasManyThrough(Score::class, GameVersion::class, 'game_id', 'game_version_id');
    }
}

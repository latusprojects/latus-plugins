<?php


namespace Latus\Plugins\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComposerRepository extends Model
{
    public const STATUS_DEACTIVATED = 0;
    public const STATUS_ACTIVATED = 1;

    protected $fillable = [
        'status', 'name', 'type', 'url'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function plugins(): HasMany
    {
        return $this->hasMany(Plugin::class);
    }

    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class);
    }
}
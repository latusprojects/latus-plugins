<?php


namespace Latus\Plugins\Models;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

    public function plugins(): Collection
    {
        return $this->hasMany(Plugin::class)->get();
    }
}
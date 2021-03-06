<?php


namespace Latus\Plugins\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plugin extends Model
{
    public const STATUS_DEACTIVATED = 0;
    public const STATUS_ACTIVATED = 1;
    public const STATUS_FAILED_INSTALL = 2;
    public const STATUS_FAILED_UPDATE = 3;
    public const STATUS_FAILED_UNINSTALL = 4;

    protected $fillable = [
        'name', 'status', 'target_version', 'repository_id',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $attributes = [
        'status' => self::STATUS_DEACTIVATED,
    ];

    public function settings(): HasMany
    {
        return $this->hasMany(PluginSetting::class);
    }

    public function repository(): BelongsTo
    {
        return $this->belongsTo(ComposerRepository::class);
    }
}
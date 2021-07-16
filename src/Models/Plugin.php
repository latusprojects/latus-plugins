<?php


namespace Latus\Plugins\Models;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    public const STATUS_DEACTIVATED = 0;
    public const STATUS_ACTIVATED = 1;
    public const STATUS_FAILED_INSTALL = 2;
    public const STATUS_FAILED_UPDATE = 3;
    public const STATUS_FAILED_UNINSTALL = 4;

    protected $fillable = [
        'name', 'status', 'target_version', 'repository_id', 'proxy_name',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function settings(): Collection
    {
        return $this->hasMany(PluginSetting::class)->get();
    }

    public function repository(): Model
    {
        return $this->belongsTo(ComposerRepository::class)->first();
    }
}
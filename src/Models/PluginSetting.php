<?php


namespace Latus\Plugins;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PluginSetting extends Model
{
    protected $fillable = [
        'plugin_id', 'key', 'value'
    ];

    public function plugin(): Collection
    {
        return $this->belongsTo(Plugin::class)->get();
    }
}
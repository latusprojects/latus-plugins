<?php


namespace Latus\Plugins\Models;


use Illuminate\Database\Eloquent\Model;

class PluginSetting extends Model
{
    protected $fillable = [
        'plugin_id', 'key', 'value'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function plugin(): Model
    {
        return $this->belongsTo(Plugin::class)->first();
    }
}
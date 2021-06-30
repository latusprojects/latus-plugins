<?php


namespace Latus\Plugins;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = [
        'name', 'status', 'source', 'type'
    ];

    public function settings(): Collection
    {
        return $this->hasMany(PluginSetting::class)->get();
    }
}
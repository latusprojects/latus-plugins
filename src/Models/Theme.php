<?php


namespace Latus\Plugins\Models;


use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    public const STATUS_ACTIVE = 0;
    public const STATUS_FAILED_INSTALL = 1;
    public const STATUS_FAILED_UPDATE = 2;
    public const STATUS_FAILED_UNINSTALL = 3;

    protected $fillable = [
        'name', 'supports', 'status', 'repository_id', 'target_version',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'supports' => 'array'
    ];

    public function repository(): Model|null
    {
        return $this->belongsTo(ComposerRepository::class)->first();
    }
}
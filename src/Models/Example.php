<?php

namespace System\Models;

use QCod\ImageUp\HasImageUploads;
use System\Scopes\StatusScope;
use Illuminate\Database\Eloquent\Builder;

class Example extends Model
{
    use HasImageUploads;

    const STATUS_ACTIVE = 1;    // 状态 有效
    const STATUS_INACTIVE = 0;  // 状态 无效

    public static $statusMap = [
        self::STATUS_ACTIVE => '有效',
        self::STATUS_INACTIVE => '无效',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setImagesField([
            'avatar' => [
                'width' => 200,
                'height' => 200,
                'rules' => 'image|max:300',
                'path' => 'uploads/images/users/avatars',
                'disk' => config('filesystems.default', 'public'),
            ],
            'image' => [
                'width' => 800,
                'height' => 800,
                'path' => 'uploads/images/examples',
            ],
        ]);
    }

    /*
     * 可以被批量赋值的属性。
     */
    protected $fillable = [];

    /*
     * 不可被批量赋值的属性。
     */
    protected $guarded = [];

    /*protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new StatusScope);
    }*/

    public function getStatusFormatAttribute()
    {
        return self::$statusMap[$this->status ?? 0];
    }
}

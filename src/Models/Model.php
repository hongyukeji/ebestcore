<?php

namespace System\Models;

use System\Traits\Models\ModelTrait;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    use ModelTrait;

    public const STATUS_DEFAULT = 0; // 默认
    public const STATUS_ACTIVE = 1; // 生效
    public const STATUS_INACTIVE = 0; // 无效

    public const STATUS = [
        self::STATUS_ACTIVE => '正常',
        self::STATUS_INACTIVE => '禁用',
    ];

    /*
     * 可以被批量赋值的属性。
     */
    //protected $fillable = [];

    /*
     * 不可被批量赋值的属性。
     */
    protected $guarded = [];

    protected $imagesUploadDisk = 'public';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->imagesUploadDisk = config('filesystems.default', 'public');
    }

    /*protected function imageUploadFilePath($file)
    {
        return md5($file->getFilename()) . $file->getClientOriginalName();
    }*/
}

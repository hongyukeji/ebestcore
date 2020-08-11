<?php

namespace System\Models;

use QCod\ImageUp\HasImageUploads;

class Shop extends Model
{
    use HasImageUploads;

    public const STATUS_ACTIVE = 1; // 正常
    public const STATUS_INACTIVE = 0; // 关闭

    public const STATUS = [
        self::STATUS_ACTIVE => '正常',
        self::STATUS_INACTIVE => '关闭',
    ];

    public const AUDIT_STATUS_REFUSE = 0;
    public const AUDIT_STATUS_PASS = 1;
    public const AUDIT_STATUS_WAIT = 10;

    public const AUDIT_STATUS = [
        self::AUDIT_STATUS_WAIT => '待审核',
        self::AUDIT_STATUS_PASS => '已通过',
        self::AUDIT_STATUS_REFUSE => '未通过',
    ];
    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = [
        'is_self', 'logo', 'switch',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setImagesField([
            'image' => [
                'path' => uploads_path('shop.image'),
                'disk' => config('filesystems.default', 'public'),
            ],
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault(function ($user) {
            $user->id = 0;
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getLogoAttribute()
    {
        if (!empty($this->image)) {
            return asset_url($this->image);
        } else {
            return asset_url(config('params.shops.default_image'));
        }
    }

    public function getProducts()
    {
        return Product::query()->where('shop_id', $this->id)->get();
    }

    public function getQueryProducts($key, $number = null, $except_ids = [])
    {
        return Product::query()
            ->whereNotIn('id', $except_ids)
            ->where([
                $key => true,
                'shop_id' => $this->id,
                'status' => 1
            ])
            ->orderBy('sort', 'desc')
            ->limit($number)
            ->get();
    }

    public function getQqLinkAttribute()
    {
        if (empty($this->qq)) {
            return null;
        }
        return "http://wpa.qq.com/msgrd?v=3&uin={$this->qq}&site=qq&menu=yes";
    }

    public function getWwLinkAttribute()
    {
        if (empty($this->ww)) {
            return null;
        }
        return "http://amos.alicdn.com/getcid.aw?v=2&uid={$this->ww}&site=cntaobao&s=1&groupid=0&charset=utf-8";
    }

    public function getIsSelfAttribute()
    {
        return empty($this->id) || $this->self_support ? true : false;
    }

    public function getSwitchAttribute()
    {
        $switch = [
            // 支持发票
            'support_invoice' => $this->support_invoice,
        ];
        return collect($switch);
    }

    public function shopType()
    {
        return $this->belongsTo(ShopType::class, 'shop_type_id', 'id')->withDefault(function ($user) {
            $user->id = 0;
        });
    }

    public function getStatusFormatAttribute()
    {
        return $this->status >= 0 ? self::STATUS[$this->status] ?? '未知状态' : self::STATUS[self::STATUS_INACTIVE];
    }

    public function getAuditStatusFormatAttribute()
    {
        return $this->audit_status >= 0 ? self::AUDIT_STATUS[$this->audit_status] ?? '未知状态' : self::AUDIT_STATUS[self::AUDIT_STATUS_WAIT];
    }
}

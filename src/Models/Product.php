<?php

namespace System\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use System\Http\Resources\ProductImageResource;

class Product extends Model
{
    use SoftDeletes;

    public const STATUS_WAREHOUSE = 0;
    public const STATUS_ON_SALE = 1;
    public const STATUS_NO_SALE = 10;
    public const STATUS_VIOLATION = 20;

    public const STATUS = [
        self::STATUS_WAREHOUSE => '仓库中',
        self::STATUS_ON_SALE => '上架',
        self::STATUS_NO_SALE => '下架',
        self::STATUS_VIOLATION => '违规',
    ];

    public const AUDIT_STATUS_WAIT = 0;
    public const AUDIT_STATUS_PASS = 1;
    public const AUDIT_STATUS_REFUSE = 10;

    public const AUDIT_STATUS = [
        self::AUDIT_STATUS_WAIT => '待审核',
        self::AUDIT_STATUS_PASS => '已通过',
        self::AUDIT_STATUS_REFUSE => '未通过',
    ];

    public const STOCK_COUNT_MODE_DEFAULT = 1;
    public const STOCK_COUNT_MODE_PAYMENT = 1;
    public const STOCK_COUNT_MODE_PLACE_ORDER = 2;

    public const STOCK_COUNT_MODE = [
        self::STOCK_COUNT_MODE_PAYMENT => '买家拍下减库存',
        self::STOCK_COUNT_MODE_PLACE_ORDER => '买家付款减库存',
    ];

    protected $fillable = [
        'category_id', 'brand_id', 'shop_id', 'product_type_id',
        'name', 'description', 'content', 'mobile_content', 'spu_code', 'keywords', 'image', 'video', 'video_url',
        'price', 'market_price', 'cost_price', 'stock', 'warning_stock',
        'is_best', 'is_hot', 'is_new', 'sale_count', 'browse_count', 'comment_count', 'favorite_count',
        'good_count', 'mid_count', 'bad_count', 'score',
        'audit_status', 'audit_remark', 'sort', 'status',
    ];

    protected $hidden = [];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = [
        'price_range', 'sku_list', 'is_sku', 'specifications', 'image_url', 'images_url', 'after_services',
    ];

    public function scopeActive($query)
    {
        return $query->where(['status' => config('params.models.status_active', true), 'audit_status' => 1]);
    }

    /*
     * 商品扩展
     */
    public function extend()
    {
        return $this->hasOne(ProductExtend::class, 'product_id', 'id')->withDefault(function ($extend) {
            $extend->virtual_sale_count = null;
            $extend->buy_url = null;
        });
    }

    /*
     * 分类
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->withDefault(function ($category) {
            $category->id = 0;
            $category->name = trans('common.default') . trans('common.category');
            $category->full_name = trans('common.default') . trans('common.category');
        });
    }

    /*
     * 品牌
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id')->withDefault(function ($brand) {
            $brand->id = 0;
            $brand->name = '';
        });
    }

    /*
     * 店铺
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id')->withDefault(function ($shop) {
            $shop->id = 0;
            $shop->name = config('shop.self_name', '平台自营');
            $shop->contact = config('websites.basic.contact.name', config('websites.basic.site_name', config('app.name')));
            $shop->contact_phone = config('websites.basic.contact.phone') ?: config('websites.basic.contact.mobile');
            $shop->contact_address = config('websites.basic.contact.address');
            $shop->qq = config('websites.basic.contact.qq');
            $shop->ww = config('websites.basic.contact.ww');
        });
    }

    /*
     * 商品图片
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort', 'asc');
    }

    /*
     * 商品评价
     */
    public function comments()
    {
        return $this->hasMany(ProductComment::class)->orderBy('created_at', 'desc');
    }

    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    /*
     * 是否是sku商品
     */
    public function getIsSkuAttribute($value)
    {
        if (isset($this->skus) && count($this->skus)) {
            return true;
        } else {
            return false;
        }
    }

    public function getPriceRangeAttribute()
    {
        if (count($this->skus) > 1) {
            return $this->skus->min('price') . '-' . $this->skus->max('price');
        } elseif (count($this->skus)) {
            return $this->skus->first()->price;
        }
        return $this->price ?? '0.00';
    }

    public function getPriceAttribute($value)
    {
        if (count($this->skus) > 1) {
            return $this->skus->min('price');
        } elseif (count($this->skus)) {
            return $this->skus->first()->price;
        }
        return $value ?? '0.00';
    }

    public function getStockAttribute($value)
    {
        if (count($this->skus)) {
            return $this->skus->sum('stock');
        }
        return $value ?? 0;
    }

    public function getImageAttribute($value)
    {
        if (empty($value)) {
            if ($image = $this->images->first()) {
                return $image->image_path;
            } else {
                return config('params.products.default_image');
            }
        }
        return $value;
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            return asset_url($this->image);
        } else {
            return asset_url(config('params.products.default_image'));
        }
    }

    public function getImagesUrlAttribute()
    {
        if ($this->images && count($this->images)) {
            return ProductImageResource::collection($this->images);
        } else {
            $images = collect([
                [
                    'id' => 0,
                    'product_id' => $this->id,
                    'image_path' => $this->image,
                    'sort' => 0,
                ]
            ]);
            return ProductImageResource::collection($images);
        }
    }

    public function getSpecificationsAttribute()
    {
        try {
            if (!empty($this->extend->specifications)) {
                $specifications = json_decode($this->extend->specifications);
                if (is_array($specifications)) {
                    return $specifications;
                }
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getAfterServicesAttribute()
    {
        if (!empty($this->extend->after_services) && is_array($this->extend->after_services)) {
            $after_services = AfterService::query()->find($this->extend->after_services);
            if ($after_services) {
                return $after_services;
            }
        }
        return null;
    }

    public function getVideoUrlAttribute($value)
    {
        if (!empty($value)) {
            // 返回商品视频外链
            return asset_url($value);
        } elseif ($this->video) {
            // 返回商品上传视频
            return asset_url($this->video);
        } else {
            // 返回空值
            return null;
        }
    }

    public function getSkuListAttribute()
    {
        try {
            $sku_templates = json_decode($this->extend->sku_template, true);
            $skus = $this->skus;
            if (!empty($sku_templates) && count($sku_templates)) {
                foreach ($sku_templates as $key => $val) {
                    foreach ($val['params'] as $k => $v) {
                        $is_exist = false;
                        foreach ($skus as $sku) {
                            if (strstr($sku->name, $v) && $sku->status) {
                                $is_exist = true;
                                break;
                            }
                        }
                        if (!$is_exist) {
                            unset($sku_templates[$key]['params'][$k]);
                        }
                    }

                    if (isset($sku_templates[$key]['params']) && count($sku_templates[$key]['params']) < 1) {
                        unset($sku_templates[$key]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
            return [];
        }

        return $sku_templates;
    }

    public function getStatusFormatAttribute()
    {
        return $this->status >= 0 ? self::STATUS[$this->status] ?? '未知状态' : self::STATUS[self::STATUS_INACTIVE];
    }

    public function getAuditStatusFormatAttribute()
    {
        return $this->audit_status >= 0 ? self::AUDIT_STATUS[$this->audit_status] ?? '未知状态' : self::AUDIT_STATUS[self::AUDIT_STATUS_WAIT];
    }

    /**
     * 防止XSS攻击
     *
     * @param $value
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = clean($value, 'text_content');
    }

    public function setMobileContentAttribute($value)
    {
        $this->attributes['mobile_content'] = clean($value, 'text_content');
    }
}

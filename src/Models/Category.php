<?php

namespace System\Models;

use Illuminate\Support\Facades\Storage;
use System\Traits\Models\CategoryTrait;
use QCod\ImageUp\HasImageUploads;

class Category extends Model
{
    use CategoryTrait, HasImageUploads;

    protected $fillable = ['name', 'description', 'image', 'parent_id', 'specification_id', 'sort', 'status',];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = [
        'image_url', 'full_name', 'parent_name'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setImagesField([
            'image' => [
                'path' => uploads_path('category'),
                'disk' => config('filesystems.default', 'public'),
            ],
        ]);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'category_to_brands', 'category_id', 'brand_id')->orderBy('id', 'asc');
    }

    public function specification()
    {
        return $this->belongsTo(ProductSpecification::class, 'specification_id', 'id')->withDefault(function ($specification) {
            $specification->id = 0;
            $specification->name = '';
        });
        //return $this->belongsToMany(ProductSpecification::class, 'category_to_specifications', 'category_id', 'product_specification_id')->orderBy('id', 'asc');
    }

    /*
     * 从当前分类，依次向上查找是否存在商品规格
     */
    public function getSpecification()
    {
        if ($items = $this->getParents()->sortKeysDesc()) {
            foreach ($items as $item) {
                if ($item->specification->id != 0) {
                    return $item->specification;
                }
            }
        }
        return null;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            return asset_url($this->image);
        } else {
            return asset_url(config('params.products.default_image'));
        }
    }
}

<?php

namespace System\Observers;

use System\Models\Product;
use System\Models\ProductExtend;
use System\Models\ProductImage;
use System\Models\ProductSku;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ProductObserver
{
    /**
     * 监听数据即将创建的事件。
     *
     * @param Product $product
     * @return void
     */
    public function creating(Product $product)
    {
        $this->defaultValues($product);
    }

    /**
     * 监听数据创建后的事件。
     *
     * @param Product $product
     * @return void
     */
    public function created(Product $product)
    {
        // 创建商品事件
        event(new \System\Events\Products\CreateProductEvent($product));

        // 创建商品扩展表
        ProductExtend::firstOrCreate([
            'product_id' => $product->id
        ]);
        if (is_null($product->spu_code) && config('params.products.product_code.status', true)) {
            $str = $product->id;
            $prefix = config('params.products.product_code.prefix', 'WMT');
            $length = config('params.products.product_code.length', 16);
            $pad_string = config('params.products.product_code.pad_string', '0');
            $product->spu_code = generate_product_code($prefix, $str, $length, $pad_string);
            $product->save();
        }
    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param Product $product
     * @return void
     */
    public function updating(Product $product)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param Product $product
     * @return void
     */
    public function updated(Product $product)
    {
        //
    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param Product $product
     * @return void
     */
    public function saving(Product $product)
    {
        /*$request = request();
        if (!$request->has('is_best')) {
            $product->is_best = '0';
        }
        if (!$request->has('is_hot')) {
            $product->is_hot = '0';
        }
        if (!$request->has('is_new')) {
            $product->is_new = '0';
        }*/
    }

    /**
     * 监听数据保存后的事件。
     *
     * @param Product $product
     * @return void
     */
    public function saved(Product $product)
    {
        $request = request();
        if (isset($product->id)) {
            // 商品视频处理
            if ($request->hasFile('video')) {
                // 删除旧文件
                $original_file = $product->getOriginal('video');
                if (Storage::exists($original_file)) {
                    Storage::delete($original_file);
                }

                // 获取文件相关信息
                $file = $request->file('video');
                $ext = $file->getClientOriginalExtension();     // 扩展名
                $file_name = uuid() . ".{$ext}";
                $file_path = $file->storeAs(uploads_path('product.video'), $file_name);

                $product->video = $file_path;
            }

            $product_image_ids = [];

            // 已存在商品图片
            if ($request->filled("images")) {
                $images = $request->input('images');
                foreach ($images as $key => $image) {
                    $orm_product_image = ProductImage::updateOrCreate([
                        'product_id' => $product->id,
                        'image_path' => $image,
                    ], [
                        'sort' => $key ?? 0,
                    ]);
                    array_push($product_image_ids, $orm_product_image->id);
                }
                ProductImage::query()->where('product_id', $product->id)->whereNotIn('id', $product_image_ids)->delete();
            }

            // 商品相册上传路径
            $image_upload_path = 'uploads/images/products/photos/' . $product->id;

            // 新上传商品图片文件
            if ($request->hasFile("image_files")) {
                $files = $request->file('image_files');
                foreach ($files as $key => $file) {
                    if ($file->isValid()) {
                        $sort = intval(ProductImage::query()->where('product_id', $product->id)->max('sort')) + ($key + 1);
                        $ext = $file->getClientOriginalExtension();
                        $file_name = uuid() . ".{$ext}";
                        $file_path = $file->storeAs($image_upload_path, $file_name);
                        $orm_product_image = ProductImage::updateOrCreate([
                            'product_id' => $product->id,
                            'image_path' => $file_path,
                        ], [
                            'sort' => $sort,
                        ]);
                        array_push($product_image_ids, $orm_product_image->id);
                    }
                }
                ProductImage::query()->where('product_id', $product->id)->whereNotIn('id', $product_image_ids)->delete();
            }

            // 删除多余图片
            $extra_image_ids = $product->images->skip(config('params.products.image_number', 5))->pluck('id');
            if ($extra_image_ids) {
                ProductImage::query()->where('product_id', $product->id)->whereIn('id', $extra_image_ids)->delete();
            }

            // 商品扩展
            if ($request->filled('extend')) {
                //$product->extend()->update($request->input('extend'));
                ProductExtend::updateOrCreate(['product_id' => $product->id], $request->input('extend'));
            }

            // 商品Sku
            if ($request->filled('skus')) {
                $sku_ids = [];
                $uuid = UUID::generate()->hex;
                $spu_code = $product->spu_code;
                foreach ($request->input('skus') as $key => $sku) {
                    if (!isset($sku['sku_code'])) {
                        $sku['sku_code'] = $spu_code ? $spu_code . '-' . ((int)$key + 1) : '';  // strtoupper($uuid)
                    }
                    if (!isset($sku['status'])) {
                        $sku['status'] = 0;
                    }
                    if (!isset($sku['price'])) {
                        $sku['price'] = 0;
                    }
                    if (!isset($sku['stock'])) {
                        $sku['stock'] = 0;
                    }
                    $orm_product_sku = ProductSku::updateOrCreate(['product_id' => $product->id, 'name' => $sku['name'] ?? ''], $sku);
                    array_push($sku_ids, $orm_product_sku->id);
                }
                ProductSku::query()->where('product_id', $product->id)->whereNotIn('id', $sku_ids)->delete();
            }
        }
    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param Product $product
     * @return void
     */
    public function deleting(Product $product)
    {
        // 判断是否是彻底删除
        if (!$product->deleted_at) {
            try {

                // 删除用户对应的扩展表数据
                ProductExtend::query()->where('product_id', $product->id)->delete();

                // 删除商品sku
                ProductSku::query()->where('product_id', $product->id)->delete();

                // 删除视频
                if (Storage::exists($product->video)) {
                    Storage::delete($product->video);
                }
            } catch (\Exception $e) {
                Log::warning($e->getMessage());
            }

            // 删除详情图片 - 电脑端
            try {
                $file_paths = collect();
                $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
                if ($product->content) {
                    preg_match_all($preg, $product->content, $allImg);
                    if (isset($allImg[1])) {
                        $file_paths = $file_paths->merge($allImg[1]);
                    }
                }
                $prefix = config('app.url');
                $file_paths->each(function ($item, $key) use ($prefix) {
                    $file = str_after($item, $prefix);
                    if (\Illuminate\Support\Facades\Storage::exists($file)) {
                        \Illuminate\Support\Facades\Storage::delete($file);
                    }
                });
            } catch (\Exception $e) {
                Log::warning($e->getMessage());
            }

            // 删除详情图片 - 手机端
            try {
                $file_paths = collect();
                $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
                if ($product->mobile_content) {
                    preg_match_all($preg, $product->mobile_content, $allImg);
                    if (isset($allImg[1])) {
                        $file_paths = $file_paths->merge($allImg[1]);
                    }
                }
                $prefix = config('app.url');
                $file_paths->each(function ($item, $key) use ($prefix) {
                    $file = str_after($item, $prefix);
                    if (\Illuminate\Support\Facades\Storage::exists($file)) {
                        \Illuminate\Support\Facades\Storage::delete($file);
                    }
                });
            } catch (\Exception $e) {
                Log::warning($e->getMessage());
            }
        }
    }

    /**
     * 监听数据删除后的事件。
     *
     * @param Product $product
     * @return void
     */
    public function deleted(Product $product)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param Product $product
     * @return void
     */
    public function restoring(Product $product)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param Product $product
     * @return void
     */
    public function restored(Product $product)
    {

    }

    public function defaultValues($product)
    {
        if (is_null($product->is_best)) {
            $product->is_best = 0;
        }
        if (is_null($product->is_hot)) {
            $product->is_hot = 0;
        }
        if (is_null($product->is_new)) {
            $product->is_new = 0;
        }
        // 价格
        if (is_null($product->price)) {
            $product->price = 0;
        }

        // 库存
        if (is_null($product->stock)) {
            $product->stock = 0;
        }
        if (is_null($product->warning_stock)) {
            $product->warning_stock = 0;
        }

        // 统计
        if (is_null($product->sale_count)) {
            $product->sale_count = 0;
        }
        if (is_null($product->browse_count)) {
            $product->browse_count = 0;
        }
        if (is_null($product->comment_count)) {
            $product->comment_count = 0;
        }
        if (is_null($product->favorite_count)) {
            $product->favorite_count = 0;
        }
        if (is_null($product->good_count)) {
            $product->good_count = 0;
        }
        if (is_null($product->mid_count)) {
            $product->mid_count = 0;
        }
        if (is_null($product->bad_count)) {
            $product->bad_count = 0;
        }

        // 店铺编号
        if (is_null($product->shop_id)) {
            $product->shop_id = 0;
        }

        // 排序
        if (is_null($product->sort)) {
            $sort = config('params.models.sort_default', 1000);
            $product->sort = $sort;
        }

        // 商品状态
        if (is_null($product->status)) {
            $product->status = true;
        }

        if (is_null($product->created_at)) {
            $product->created_at = now();
        }
        if (is_null($product->updated_at)) {
            $product->updated_at = now();
        }
    }
}

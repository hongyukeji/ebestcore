<?php

namespace System\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use System\Models\Brand;
use System\Models\Category;
use System\Models\CategoryToBrand;
use System\Models\Product;
use System\Models\ProductImage;

class HelperController extends Controller
{
    /*
     * 获取商品数据源
     */
    public function productGet(Request $request)
    {
        ini_set('memory_limit', '10240M');
        set_time_limit(0); // 设置超时限制为0分钟

        $products = [];
        try {
            $http = new \GuzzleHttp\Client;
            $response = $http->get('http://gongct.test/api/products', [
                'verify' => false,
                //'timeout' => 0,
                'query' => $request->query(),
            ]);
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody(), true);
                $products = $data['data'];
                Log::info("远程商品数据获取完成");
                $file = public_path('uploads/products.php');
                $array = $products;
                $text = "<?php\n\nreturn " . var_export($array, true) . ';';
                if (false !== fopen($file, 'w+')) {
                    file_put_contents($file, $text);
                } else {
                    echo '创建失败';
                }
            }
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
        }
        return "远程商品数据获取完成";
    }

    /*
     * 将获取的商品数据源导入当前数据库
     */
    public function productImport($id)
    {
        $product_file_array = require_once public_path('uploads/products.php');
        //$products[] = $product_file_array[0];
        //$products[] = $product_file_array[1];
        $products = $product_file_array;
        foreach ($products as $key => $product) {
            /*if ($key > 1000) {
                break;
            }*/

            // 创建分类
            $parent_id = 0;
            if (!empty($product['categories']) && count($product['categories'])) {
                foreach ($product['categories'] as $category_index => $category_data) {
                    $category = Category::updateOrCreate([
                        'name' => $category_data['name'] ?? '默认分类',
                    ], [
                        'image' => !empty($category_data['image']) ? $this->image_url($category_data['image']) : '',
                        'parent_id' => $parent_id,
                        'sort' => $category_data['sort_order'] ?? null,
                        'status' => $category_data['status'] ?? null,
                        'created_at' => $category_data['created_at'] ?? now(),
                        'updated_at' => $category_data['updated_at'] ?? now(),
                    ]);
                    $parent_id = $category->id;
                    if ($category_index == count($product['categories']) - 1) {
                        $product_category = $category;
                    }
                }
            } else {
                $product_category = Category::updateOrCreate([
                    'name' => $product['category']['name'] ?? '默认分类',
                ], [
                    'image' => !empty($product['category']['image']) ? $this->image_url($product['category']['image']) : '',
                    'parent_id' => $product['category']['parent_id'] ?? 0,
                    'sort' => $product['category']['sort_order'] ?? null,
                    'status' => $product['category']['status'] ?? null,
                    'created_at' => $product['category']['created_at'] ?? now(),
                    'updated_at' => $product['category']['updated_at'] ?? now(),
                ]);
            }

            // 创建品牌
            if (!empty($product['brand'])) {
                $product_brand = Brand::updateOrCreate([
                    'name' => $product['brand']['name'] ?? '默认品牌',
                ], [
                    'description' => $product['brand']['description'],
                    'image' => !empty($product['brand']['image']) ? $this->image_url($product['brand']['image']) : '',
                    'sort' => $product['brand']['sort_order'] ?? null,
                    'status' => $product['brand']['status'] ?? null,
                    'created_at' => $product['brand']['created_at'] ?? now(),
                    'updated_at' => $product['brand']['updated_at'] ?? now(),
                ]);
                if (!empty($product['brand']['category'])) {
                    $product_brand_category = Category::updateOrCreate([
                        'name' => $product['brand']['category']['name'] ?? '默认分类',
                    ], []);
                    $category_to_brand = CategoryToBrand::updateOrCreate([
                        'category_id' => $product_brand_category->id,
                        'brand_id' => $product_brand->id,
                    ], []);
                }
            }

            // 创建商品
            $orm_product = Product::updateOrCreate([
                'name' => $product['name'] ?? '',
            ], [
                'category_id' => $product_category['id'] ?? 0,
                'brand_id' => $product_brand['id'] ?? 0,
                'shop_id' => 0,
                'spu_code' => $product['spu_code'],
                'description' => $product['description'],
                'content' => $product['content'],
                'image' => !empty($product['image']) ? $this->image_url($product['image']) : '',
                'video' => $product['video'],
                'price' => $product['price'],
                'market_price' => $product['market_price'],
                'cost_price' => $product['cost_price'],
                'stock' => $product['stock'],
                'sort' => $product['sort_order'] ?? null,
                'status' => $product['status'] ?? null,
                'created_at' => $product['created_at'] ?? now(),
                'updated_at' => $product['updated_at'] ?? now(),
            ]);

            // 创建图片
            foreach ($product['images'] as $image) {
                $product_image = ProductImage::updateOrCreate([
                    'product_id' => $orm_product->id,
                    'image_path' => !empty($image['original_image']) ? $this->image_url($image['original_image']) : '',
                ], [
                    'sort' => $image['sort_order'] ?? null,
                    'created_at' => $image['created_at'] ?? now(),
                    'updated_at' => $image['updated_at'] ?? now(),
                ]);
            }
        }
        Log::info("商品批量导入工作完成！");
        return "商品批量导入工作完成！";
    }

    public function image_url($image)
    {
        if (starts_with($image, 'uploads')) {
            return "/$image";
        }
        return $image;
    }
}

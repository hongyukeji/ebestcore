<?php

namespace System\Http\Controllers\Test;

use Carbon\CarbonInterval;
use Hongyukeji\YiLianYunOrderPrint\Sdk\YiLianYunPrinter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Modules\Cloud\Models\Update;
use System\Librarys\PrintDevice\PrintDeviceUtil;
use System\Models\Article;
use System\Models\ArticleCategory;
use System\Models\Category;
use System\Models\Env;
use System\Models\Menu;
use System\Models\Navigation;
use System\Models\Order;
use System\Models\PaymentLog;
use System\Models\Product;
use System\Models\ProductImage;
use System\Models\Region;
use System\Models\Shop;
use System\Models\Slider;
use System\Models\UserAddress;
use GuzzleHttp\Exception\GuzzleException;
use Hongyukeji\LaravelSettings\Facades\Settings;
use Hongyukeji\Plugin\Event as Event;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use System\Librarys\Payment\Payment;
use System\Models\CodeModel;
use System\Services\CategoryService;
use System\Services\MenuService;
use System\Services\OrderService;
use System\Services\PluginService;
use System\Services\ProductService;
use System\Services\RegionService;
use System\Utils\Util\Facades\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class IndexController extends Controller
{
    public function index()
    {
        return $this->test();
    }

    public function test()
    {
        //
    }

    public function querySql()
    {
        DB::connection()->enableQueryLog(); // 开启查询日志
        DB::table("orders")
            ->whereNotNull('payment_at')
            ->get();
        $queries = DB::getQueryLog(); // 获取查询日志
        dd($queries);
    }

    public function count()
    {
        $days = request('days', 7);
        $range = Carbon::today()->subDays($days);
        $items = Order::where('created_at', '>=', $range)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get([
                \DB::raw('Date(created_at) as date'),
                \DB::raw('sum(total_amount) AS total_amount'),
                \DB::raw('sum(CASE WHEN id > 0 THEN 1 ELSE 0 END) AS total_amount_number'),
                \DB::raw('sum(CASE WHEN `payment_at` is not null THEN total_amount ELSE 0 END) AS amount_paid'),
                \DB::raw('sum(CASE WHEN `payment_at` is not null THEN 1 ELSE 0 END) AS amount_paid_number'),
            ]);
        dd($items);
    }

    public function PrintOrder()
    {
        $YiLianYunPrinter = new YiLianYunPrinter();
        $order = Order::query()->find(52);
        $result = $YiLianYunPrinter->print($order);
        if (!$result->verify()) {
            dump($result->message);
        }
        dd($result);
    }

    public function PrintDeviceUtil()
    {
        $PrintDeviceUtil = new PrintDeviceUtil();
        $code = $PrintDeviceUtil->testPrint();
        dump($code);
    }

    public function updateQuery()
    {
        $updates = Update::query()
            ->where('status', true)
            //->where('version', '>', $version)
            ->where('version_type', '>=', '1.0.0')
            ->orderBy('version', 'asc')
            ->get(['version', 'version_type', 'file_size', 'code', 'description']);
        dd($updates);
    }

    public function deleteDirectory()
    {
        $paths = [
            'plugins/hongyukeji/auto-close-unpaid-order',
            'plugins/hongyukeji/visitor-statistics',
            'modules/AutomaticDelivery',
            'modules/Cloud',
            'modules/Installer',
            'modules/WeChat',
        ];
        foreach ($paths as $path) {
            if (\Illuminate\Support\Facades\Storage::disk('root')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('root')->deleteDirectory($path);
            }
        }
    }

    /*
     * 获得变量的类型
     */
    public function gettype()
    {
        $str = "";
        dump(gettype($str));
    }

    public function time()
    {
        $s = config('params.orders.payment_ttl');
        $s = 60 * 60 * 12;
        $payment_ttl = now()->diffForHumans(now()->addSeconds($s), true);
        dump($payment_ttl);
        dd(time_format_second($s));
    }

    public function version_check()
    {
        dd(version_require('3.3', '>=3.3'));
    }

    public function version_create()
    {
        $version = '1.2.3';
        $version_array = explode('.', $version);
        $last = array_pop($version_array);
        $version_array[count($version_array)] = $last + 1;
        $new_version = implode('.', $version_array);
        dd($new_version);
    }

    public function empower()
    {
        $empower_status = get_system_empower_status();
        dump($empower_status);
    }

    public function encrypt()
    {
        if (!function_exists('beast_encode_file')) {
            abort('系统内核升级，请联系官方技术人员进行服务器环境重新部署');
        } else {
            echo 1;
        }
        phpinfo();
    }

    public function url()
    {
        $url = 'http://demo.wmt.ltd';
        dump(Util::shortUrl()->generate($url));
        $arr = parse_url($url);
        dd($arr);
    }

    public function region()
    {
        $region_service = new \System\Services\RegionService();
        // 查询省份
        $province = $region_service->getProvince('江苏');
        dump($province->toArray());
        // 查询城市
        $city = $region_service->getCity('苏州', $province->id);
        dump($city->toArray());
        // 查询区域
        $district = $region_service->getDistrict('张家港', $city->id);
        dump($district->toArray());
    }

    public function ip()
    {
        $ip = '127.0.0.1';
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            echo "it's valid";
        } else {
            echo "it's not valid";
        }
        dd(geoip($ip));
    }

    public function redis()
    {
        //error_log('一些信息。');
        Redis::set('name', 'guwenjie');
        $values = Redis::get('name');
        dd($values);
    }

    public function update33502()
    {
        \Illuminate\Support\Facades\Artisan::call('sync:demo', ["--force" => true]);
        $navigations = \System\Models\Navigation::query()->where('group', 'mobile_home_navigation')->where('image', 'like', '/assets/mobile/img/index/nav_img%.png')->delete();
        dd($navigations);
    }

    public function zip()
    {
        ini_set('memory_limit', '-1');  // -1 取消内存限制
        ini_set('max_execution_time', '0');
        ignore_user_abort(true);    // 关掉浏览器，PHP脚本也可以继续执行
        set_time_limit(0);  // 通过set_time_limit(0)可以让程序无限制的执行下去

        $file_url = '';
        $base_path = 'storage/app/update';
        $path = base_path($base_path);
        mk_folder($path);
        $file_name = basename($file_url);
        $file_path = str_finish($path, '/') . $file_name;
        //$out_path = base_path();
        $out_path = str_finish($path, '/') . 'unzip';
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->get($file_url);
        if ($response->getStatusCode() == 200) {
            // 下载文件
            $file_zip = $response->getBody()->getContents();
            $filesystem = new \Illuminate\Filesystem\Filesystem();
            $filesystem->put($file_path, $file_zip);

            // 解压文件
            $zip = new \ZipArchive();
            $open_res = $zip->open($file_path);
            if ($open_res === true) {
                $zip->extractTo($out_path);
                $zip->close();
            }

            // 删除文件
            if ($filesystem->exists($file_path)) {
                $filesystem->delete($file_path);
            }

            // 清空文件夹
            //$filesystem->cleanDirectory($path);
        }
    }

    public function testDingoApi()
    {
        dd(app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('categories.show'));
    }

    public function update_3_3_280()
    {
        \System\Models\Navigation::query()->where('group', 'mobile_home_navigation')->where('title', 'like', '%手机端-首页-导航-%')->delete();
        \System\Models\Slider::query()->where('group', 'mobile_home_slider')->where('image', 'like', '%/assets/mobile/img/index/swiper0%')->delete();
    }

    public function products()
    {
        $product_service = new ProductService();
        dd($product_service->getBests(15));
    }

    public function session()
    {
        $session = request()->session()->getId();
        $product = Product::query()->first();
        event(new \System\Events\Products\BrowseProductEvent($product, $session));
        dd(class_basename(__CLASS__));
    }

    public function strUrl()
    {
        $return_url = 'http://ebestmall.test/seller/systems/routes';
        $url_array = parse_url($return_url);
        $prefix = str_start(config('systems.routes.seller.prefix', 'seller'), '/');
        dump(starts_with($url_array['path'], $prefix));
    }

    public function carbon()
    {
        dump(str_replace('-', '', now()->subMonth()->toDateString()));
        dump(now()->subMonth()->toDateString());

        dump(now()->addMonth());

        dump(now()->subDay());

        echo Carbon::now()->subDays(5)->diffForHumans();               // 5天前

        echo Carbon::now()->diffForHumans(Carbon::now()->subYear());   // 1年后

        $dt = Carbon::createFromDate(2011, 8, 1);

        echo $dt->diffForHumans($dt->copy()->addMonth());              // 1月前
        echo $dt->diffForHumans($dt->copy()->subMonth());              // 11月后

        echo Carbon::now()->addSeconds(5)->diffForHumans();            // 5秒距现在

        echo Carbon::now()->subDays(24)->diffForHumans();              // 3周前
        echo Carbon::now()->subDays(24)->diffForHumans(null, true);    // 3周
    }

    public function chat()
    {
        $chat = Chat::users()->get(implode(',', ['shadow', 'imuser123']));
        $user_info = [
            'nick' => 'Shadow', // 昵称
            'icon_url' => 'http://wmt.ltd/assets/inspinia/img/landing/user7-160x160.jpg',   // 头像
            'email' => 'admin@hongyuvip.com',
            'mobile' => '13800138000',
            'userid' => 'shadow',
            'password' => '123456',
        ];
        //$chat = Chat::users()->add($user_info);
        dd($chat);
    }

    public function setting()
    {
        //
    }

    public function sendSms()
    {
        $result = sms_send([
            '13952101395',
            '13885180421',
        ], 'verify_code', ['code' => '123456']);
        dd($result);
    }

    /*
     * 队列 [请注意更改业务逻辑后，必须重启队列才能生效执行更改后的业务逻辑]
     */
    public function queue()
    {
        dispatch(new \System\Jobs\SendEmail('admin@hongyuvip.com', '队列 - 测试邮件'));
        $this->dispatch(new \System\Jobs\SendEmail('ebestmall@qq.com', '队列 - 测试邮件'));
    }

    public function sendMail()
    {
        Mail::raw('测试邮件', function ($message) {
            $message->to('example@example.com');
        });
    }

    public function payments()
    {
        $payment = new Payment();
        $order = [
            'order_no' => '20191009000001',
            'total_amount' => '0.01',
            'subject' => '测试订单',
        ];
        //$result = $payment->gateway('payjs', 'web', $order)->send();
        $result = $payment->gateway('alipay', 'web', $order)->send();
        return $result;
    }

    public function plugin()
    {
        $plugin_service = new PluginService();
        $plugin = $plugin_service->getPlugin('hongyukeji/example');
        $config = $plugin->getConfig();
        dump($config);
        $plugin->setConfig(['extra' => ['sort' => 101100]]);
        //$json_file = $plugin->getDir() . 'composer.json';
        //$json_string = json_encode($config, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        //file_put_contents($json_file, $json_string);
        dd($plugin);
    }

    public function shop()
    {
        $shop = Shop::query()->first();
        dd($shop->getQueryProducts('is_hot', 5));
    }

    public function indexOld(Request $request)
    {
        $action = $request->input('action');
        return $this->{$action}();
    }

    public function app()
    {
        dd(app()->isLocal());
    }

    public function update()
    {
        dd('update');
    }

    public function product()
    {
        $product = Product::query()->with([
            'skus', 'skus.attributeValues', 'skus.attributeValues.attributeName',
            'specs', 'specs.specGroup', 'specs.specName', 'specs.specValue',
        ])->find(1);
        return $product->toJson();
    }

    public function payment()
    {
        $order = [
            'subject' => 'test',
            'out_trade_no' => date('YmdHis') . mt_rand(1000, 9999),
            'total_amount' => '0.01',
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
        ];
        $url = Payment::alipay()->web($order);
        dd($url);
        return redirect($url);
    }

    public function route()
    {
        dump(Route::getRoutes());
        dd(Route::has('short-url.show'));
    }

    public function array()
    {
        //
    }

    public function env()
    {
        dump(Storage::disk('root')->exists('.env.example'));
        Storage::disk('root')->copy('.env.example', '.env_');
        $envs = [
            'DEMO' => 'demo',
            'TEST' => 'test',
        ];
        set_env($envs);
    }

    public function md5()
    {
        //Util::TestGenerate()->start();
    }

    public function order()
    {
        // 获取支付表对应订单总金额
        $payment = \System\Models\Payment::find(1);
        dd($payment->orders->sum('total_amount'));

        // 获取订单支付数据
        $order = Order::find(1);
        dd($order->payment);
    }

    public function functions()
    {
        //
    }

    public function geoip()
    {
        $geoip = geoip('127.0.0.1');
        dd($geoip);
        //$geoip = get_ip_lookup('117.188.21.187', ['lang' => config('app.locale')]);dd($geoip);
    }

    public function cookie()
    {
        $cookies = request()->cookie();
        dd($cookies);
    }

    public function const()
    {
        dd(CodeModel::STATUS_ACTIVE);
    }

    public function function ()
    {
        \Illuminate\Support\Facades\Artisan::call("sync:menu", ["--force" => true, "--ansi" => true]);
        \Illuminate\Support\Facades\Artisan::call("migrate", ["--force" => true, "--ansi" => true]);
        $function = '\Illuminate\Support\Facades\Artisan::call("sync:menu", ["--force" => true, "--ansi" => true]);';
        //@eval($function);
    }

    public function envModel()
    {
        $envs = new Env();
        dd($envs->paginate());
    }

    /*
     * Laravel 响应：流式下载
     */
    public function streamDownload()
    {
        return response()->streamDownload(function () {
            echo file_get_contents("https://github.com/laravel/laravel/blob/master/readme.md");
        }, 'laravel-readme.md');
    }

    public function category()
    {
        $category = new Category();
        $parents = $category->findParents(3);
        dd($parents);
    }

    public function model()
    {
        dd(Product::STATUS);
    }

    public function table()
    {
        // 获取数据库所有表名称
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        dd($tables);
    }

    public function migration()
    {
        $files = \Illuminate\Support\Facades\Storage::disk('root')->files('database/migrations');
        foreach ($files as $file) {
            $migrations = [
                'admins', 'users', 'user_extends',
                'settings', 'permission',
                'articles', 'article_categories', 'article_comments',
            ];

            $contains = str_contains($file, $migrations);
            if (!$contains && \Illuminate\Support\Facades\Storage::disk('root')->exists($file)) {
                \Illuminate\Support\Facades\Storage::disk('root')->delete($file);
            }
        }
        dd($files);
    }

    public function migrations()
    {
        /*更新菜单*/
        \Illuminate\Support\Facades\DB::table('menus')->truncate();
        $menus = config('menus', []);
        $menuService = new \System\Services\MenuService();
        $menuService->batchImport($menus);

        if (\Illuminate\Support\Facades\Storage::disk('root')->exists('app/Http/Controllers/Backend/Systems/ManagersController.php')) {
            \Illuminate\Support\Facades\Storage::disk('root')->delete('app/Http/Controllers/Backend/Systems/ManagersController.php');
        }
        $current_version = config('app.version');
        if (version_compare($current_version, '3.2.3306', '<')) {
            \Illuminate\Support\Facades\Artisan::call('composer:autoload');
            /*删除迁移文件*/
            $files = \Illuminate\Support\Facades\Storage::disk('root')->files('database/migrations');
            foreach ($files as $file) {
                $migrations = [
                    'admins', 'users', 'user_extends',
                    'settings', 'permission', 'menus',
                    'articles', 'article_categories', 'article_comments',
                ];

                $contains = str_contains($file, $migrations);
                if (!$contains && \Illuminate\Support\Facades\Storage::disk('root')->exists($file)) {
                    \Illuminate\Support\Facades\Storage::disk('root')->delete($file);
                }
            }
            /*删除旧的助手文件*/
            if (\Illuminate\Support\Facades\Storage::disk('root')->exists('system/Helpers/private.php')) {
                \Illuminate\Support\Facades\Storage::disk('root')->delete('system/Helpers/private.php');
            }
            /*删除目录*/
            $directories = [
                'resources/views/frontend/ebestmall',
                'resources/views/mobile/jd',
                'docs',
            ];
            foreach ($directories as $directory) {
                \Illuminate\Support\Facades\Storage::disk('root')->deleteDirectory($directory);
            }
            /*清理排除列表*/
            $migrations = [
                'admins', 'users', 'user_extends',
                'settings', 'permission', 'menus',
                'articles', 'article_categories', 'article_comments',
            ];
            \Illuminate\Support\Facades\Artisan::call("clear:migration", ["migrations" => $migrations, "--option" => 'exclude']);
            $tables = [
                'admins', 'users', 'user_extends', 'menus',
                'settings', 'permissions', 'roles', 'model_has_permissions', 'model_has_roles', 'role_has_permissions',
                'articles', 'article_categories', 'article_comments',
            ];
            \Illuminate\Support\Facades\Artisan::call("clear:database", ["tables" => $tables, "--option" => 'exclude']);
            \Illuminate\Support\Facades\Artisan::call("sync:menu", ["--force" => true]);
            \Illuminate\Support\Facades\Artisan::call('migrate', ["--force" => true]);
            (new \System\Services\AdvertService())->batchImport(config('demo.adverts', []));
            (new \System\Services\LinkService())->batchImport(config('demo.links', []));
            (new \System\Services\NavigationService())->batchImport(config('demo.navigations', []));
            (new \System\Services\SliderService())->batchImport(config('demo.sliders', []));
            if (function_exists('exec')) {
                $php = php_run_path();
                exec("cd " . base_path() . " && {$php} sync:menu --force && {$php} migrate --force ");
            }
        }
    }

}

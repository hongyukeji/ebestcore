<?php

if (config('systems.routes.backend.status', true)) {
    Route::group([
        'namespace' => 'Backend',
        'prefix' => config('systems.routes.backend.prefix', 'admin'),
        'as' => 'backend.',
        'domain' => config('systems.routes.backend.domain', '')
    ], function () {
        Route::group([
            'middleware' => ['auth.admin:admin', 'auth.permission:admin,resource'],
        ], function () {
            // 例子
            Route::resource('examples', 'ExamplesController');

            // 助手
            //Route::resource('helpers', 'HelperController');

            Route::get('/', 'IndexController@index')->name('index');
            Route::get('clears/cache', 'ClearsController@cache')->name('clears.cache');

            // 上传
            Route::group(['namespace' => 'Uploads', 'prefix' => 'uploads', 'as' => 'uploads.'], function () {
                // 图片 - Simditor
                Route::post('images/simditor', 'ImageController@simditor')->name('images.simditor');
            });

            // 店铺
            Route::group(['namespace' => 'Shops', 'prefix' => 'shops', 'as' => 'shops.'], function () {
                // 店铺 - 店铺
                Route::get('/', 'ShopsController@index')->name('index');
                Route::post('/', 'ShopsController@store')->name('store');
            });

            // 统计
            Route::group(['namespace' => 'Stats', 'prefix' => 'stats', 'as' => 'stats.'], function () {
                // 统计 - 销售统计
                Route::resource('sales', 'SalesController', ['only' => ['index']]);
                // 统计 - 用户统计
                Route::resource('users', 'UsersController', ['only' => ['index']]);
                // 统计 - 订单统计
                Route::resource('orders', 'OrdersController', ['only' => ['index']]);
                // 统计 - 店铺统计
                Route::resource('shops', 'ShopsController', ['only' => ['index']]);
            });

            // 设置
            Route::group(['namespace' => 'Settings', 'prefix' => 'settings', 'as' => 'settings.'], function () {
                // 全局设置 - 短信设置
                Route::get('sms/init', 'SmsController@init')->name('sms.init');
                Route::resource('sms', 'SmsController', ['only' => ['index', 'store']]);
                // 全局设置 - 邮件设置
                Route::resource('mail', 'MailController', ['only' => ['index', 'store']]);
                // 全局设置 - 支付方式
                Route::resource('payments', 'PaymentsController');
                // 全局设置 - 配送方式
                Route::resource('shippings', 'ShippingController', ['only' => ['index', 'store']]);
                // 全局设置 - 地区设置
                Route::resource('regions', 'RegionsController');
                // 全局设置 - 合作登录
                Route::resource('oauth', 'OAuthController', ['only' => ['index', 'store']]);
                // 全局设置 - IP查询
                Route::resource('geoip', 'GeoIpController', ['only' => ['index', 'store']]);
            });

            // 网站管理
            Route::group(['namespace' => 'Sites', 'prefix' => 'sites', 'as' => 'sites.'], function () {
                // 网站管理 - 基本设置
                Route::resource('bases', 'BasesController', ['only' => ['index', 'store']]);
                // 网站管理 - 店铺设置
                Route::resource('shops', 'ShopsController', ['only' => ['index', 'store']]);
                // 网站管理 - 主题设置
                Route::resource('themes', 'ThemesController', ['only' => ['index', 'store']]);
                // 网站管理 - 导航管理
                Route::resource('navigations', 'NavigationsController');
                // 网站管理 - 轮播图管理
                Route::resource('sliders', 'SlidersController');
                // 网站管理 - 友情链接
                Route::resource('links', 'LinksController');
                // 全局设置 - 拦截设置
                Route::resource('intercept', 'InterceptController', ['only' => ['index', 'store']]);
                // 全局设置 - 注册设置
                Route::resource('register', 'RegisterController', ['only' => ['index', 'store']]);
                // 全局设置 - 商品设置
                Route::resource('product', 'ProductController', ['only' => ['index', 'store']]);
                // 网站管理 - 订单设置
                Route::resource('orders', 'OrdersController', ['only' => ['index', 'store']]);
                // 网站管理 - 演示数据
                Route::resource('demo-data', 'DemoDataController', ['only' => ['index', 'store']]);
            });

            // 系统管理
            Route::group(['namespace' => 'Systems', 'prefix' => 'systems', 'as' => 'systems.'], function () {
                // 系统管理 - 基本设置
                Route::resource('bases', 'BasesController', ['only' => ['index', 'store']]);
                // 系统管理 - 数据库设置
                Route::resource('databases', 'DataBasesController', ['only' => ['index', 'store']]);
                // 系统管理 - 缓存设置
                Route::resource('caches', 'CachesController', ['only' => ['index', 'store']]);
                // 系统管理 - 会话设置
                Route::resource('session', 'SessionController', ['only' => ['index', 'store']]);
                // 系统管理 - 存储设置
                Route::resource('filesystems', 'FilesystemsController', ['only' => ['index', 'store']]);
                // 系统管理 - 路由设置
                Route::get('routes/init', 'RoutesController@init')->name('routes.init');
                Route::resource('routes', 'RoutesController', ['only' => ['index', 'store']]);
                // 系统管理 - 菜单设置
                Route::post('menus/ajax', 'MenusController@ajax')->name('menus.ajax');
                Route::resource('menus', 'MenusController', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);
                // 权限控制 - 防护开关; 开启后只有超级管理员才能访问
                $protect_permissions = config('systems.security.system_protection', false)
                    ? 'role.admin:' . config('systems.security.administrator', 'Administrator')
                    : [];
                Route::group(['middleware' => $protect_permissions], function () {
                    // 系统管理 - 权限管理 - 管理员列表
                    Route::get('admins/roles/{admin}', 'AdminsController@showRolesForm')->name('admins.roles.show');
                    Route::post('admins/roles/{admin}', 'AdminsController@roles')->name('admins.roles.store');
                    Route::resource('admins', 'AdminsController');
                    // 系统管理 - 权限管理 - 角色列表
                    Route::get('roles/permissions/{role}', 'RolesController@showPermissionsForm')->name('roles.permissions.show');
                    Route::post('roles/permissions/{role}', 'RolesController@permission')->name('roles.permissions.store');
                    Route::resource('roles', 'RolesController');
                    // 系统管理 - 权限管理 - 权限列表
                    Route::resource('permissions', 'PermissionsController');
                });
                // 系统管理 - 安全设置
                Route::resource('security', 'SecurityController', ['only' => ['index', 'store']]);
                // 系统管理 - 系统开发 - Telescope
                Route::resource('telescope', 'TelescopeController', ['only' => ['index', 'store']]);
                Route::get('telescope/clear', 'TelescopeController@clear')->name('telescope.clear');
                Route::get('telescope/prune', 'TelescopeController@prune')->name('telescope.prune');
                // 系统管理 - 队列管理
                Route::resource('queue', 'QueueController', ['only' => ['index', 'store']]);
                Route::get('queue/clear', 'QueueController@clear')->name('queue.clear');
                Route::get('queue/work', 'QueueController@work')->name('queue.work');
                Route::get('queue/restart', 'QueueController@restart')->name('queue.restart');
                Route::get('queue/retry', 'QueueController@retry')->name('queue.retry');
                // 系统管理 - 日志管理
                Route::resource('log', 'LogController', ['only' => ['index', 'store']]);
                Route::get('log/clear', 'LogController@clear')->name('log.clear');
                // 系统管理 - 系统开发 - 字体图标
                Route::resource('font-icon', 'FontIconController', ['only' => ['index']]);
                // 系统管理 - 更新 - 在线更新
                Route::post('update/auto-update', 'UpdateController@autoUpdate')->name('update.auto-update');
                Route::any('update/system', 'UpdateController@system')->name('update.system');
                Route::get('update/website-open', 'UpdateController@websiteOpen')->name('update.website-open');
                Route::get('update/website-close', 'UpdateController@websiteClose')->name('update.website-close');
                Route::get('update/release-note', 'UpdateController@releaseNote')->name('update.release-note');
                Route::get('update', 'UpdateController@index')->name('update.index');
                Route::post('update', 'UpdateController@store')->name('update.store');
                //Route::resource('update', 'UpdateController', ['only' => ['index', 'store']]);
            });

            // 终端
            Route::group(['namespace' => 'Terminal', 'prefix' => 'terminal', 'as' => 'terminal.'], function () {
                Route::group(['namespace' => 'Api', 'prefix' => 'api', 'as' => 'api.'], function () {
                    Route::resource('bases', 'BasesController', ['only' => ['index', 'store']]);
                    Route::resource('sliders', 'SlidersController', ['only' => ['index', 'store']]);
                    Route::resource('navigations', 'NavigationsController', ['only' => ['index', 'store']]);
                    Route::resource('adverts', 'AdvertsController', ['only' => ['index', 'store']]);
                    Route::resource('articles', 'ArticlesController', ['only' => ['index', 'store']]);
                    Route::resource('links', 'LinksController', ['only' => ['index', 'store']]);
                });
                Route::group(['namespace' => 'Mobile', 'prefix' => 'mobile', 'as' => 'mobile.'], function () {
                    Route::resource('bases', 'BasesController', ['only' => ['index', 'store']]);
                    Route::resource('sliders', 'SlidersController', ['only' => ['index', 'store']]);
                    Route::resource('navigations', 'NavigationsController', ['only' => ['index', 'store']]);
                    Route::resource('adverts', 'AdvertsController', ['only' => ['index', 'store']]);
                    Route::resource('articles', 'ArticlesController', ['only' => ['index', 'store']]);
                });
                Route::group(['namespace' => 'Web', 'prefix' => 'web', 'as' => 'web.'], function () {
                    Route::resource('articles', 'ArticlesController', ['only' => ['index', 'store']]);
                    Route::resource('adverts', 'AdvertsController', ['only' => ['index', 'store']]);
                });
            });

            // 应用市场
            Route::any('applications/download', 'ApplicationsController@download')->name('applications.download');
            Route::resource('applications', 'ApplicationsController');

            // 用户
            Route::resource('users', 'UsersController');
            Route::group(['namespace' => 'User'], function () {
                Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
                    // 用户等级
                    Route::resource('grades', 'GradesController');
                    // 用户账户
                    Route::resource('accounts', 'AccountsController');
                    // 提现申请
                    Route::resource('cash-withdrawals', 'CashWithdrawalsController');
                });
            });

            // 商品
            Route::put('products/batch', 'ProductsController@batchUpdate')->name('products.batch');
            Route::resource('products', 'ProductsController');
            Route::group(['namespace' => 'Product'], function () {
                Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
                    // 分类
                    Route::resource('categories', 'CategoryController');
                    // 品牌
                    Route::resource('brands', 'BrandsController');
                    // 规格
                    Route::resource('specifications', 'SpecificationsController');
                    // 回收站
                    Route::resource('recycles', 'RecyclesController', ['only' => ['index', 'update', 'destroy']]);
                });
            });
            // 订单
            Route::post('orders/shipping/{order}', 'OrdersController@shipping')->name('orders.shipping');
            Route::resource('orders', 'OrdersController');
            // 文章分类
            Route::resource('article-categories', 'ArticleCategoriesController');
            // 文章
            Route::resource('articles', 'ArticlesController');
            // 广告
            Route::resource('adverts', 'AdvertsController');
            // 页面
            Route::resource('pages', 'PagesController');
            // 活动
            Route::resource('activities', 'ActivitiesController');
            // 插件
            Route::get('plugins', 'PluginsController@index')->name('plugins.index');
            Route::post('plugins/update', 'PluginsController@update')->name('plugins.update');
            Route::get('plugins/show', 'PluginsController@show')->name('plugins.show');
            Route::delete('plugins/destroy', 'PluginsController@destroy')->name('plugins.destroy');
            // 模块
            Route::resource('modules', 'ModulesController');
            // 店铺
            Route::resource('shops', 'ShopsController');
            // 店铺等级
            Route::resource('shop-grades', 'ShopGradesController');
            // 店铺类型
            Route::resource('shop-types', 'ShopTypesController');
            // 售后服务
            Route::resource('after-services', 'AfterServicesController');
            // 管理员
            Route::resource('manager', 'ManagerController');
            // 回收站
            Route::group(['namespace' => 'Recycles'], function () {
                Route::group(['prefix' => 'recycles', 'as' => 'recycles.'], function () {
                    // 回收站 - 商品
                    Route::resource('products', 'ProductsController');
                    // 回收站 - 订单
                    Route::resource('orders', 'OrdersController');
                    // 回收站 - 会员
                    Route::resource('users', 'UsersController');
                });
            });
        });

        Route::namespace('Auth')->prefix('auth')->name('auth.')->group(function () {
            // 用户身份验证相关的路由
            Route::get('login', 'LoginController@showLoginForm')->name('login');
            Route::post('login', 'LoginController@login');
            Route::post('logout', 'LoginController@logout')->name('logout');

            // 用户注册相关路由
            Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
            Route::post('register', 'RegisterController@register');

            // 密码重置相关路由
            Route::get('forget/password', 'ForgotPasswordController@showLinkRequestForm')->name('forget.password');
            Route::post('forget/verify-code', 'ForgotPasswordController@sendVerifyCode')->name('forget.verify-code');
            Route::get('forget/verify', 'ForgotPasswordController@showVerifyForm')->name('forget.verify');
            Route::post('forget/verify', 'ForgotPasswordController@verify');

            //Route::get('forget/reset', 'ResetPasswordController@reset')->name('forget.reset');
            //Route::post('forget/reset-password', 'ResetPasswordController@reset')->name('forget.reset-password');

            Route::get('password/reset', 'ResetPasswordController@showResetForm')->name('password.reset');
            Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');
        });
    });
}

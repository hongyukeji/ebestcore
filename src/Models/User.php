<?php

namespace System\Models;

use System\Events\Users\UserDeleted;
use System\Events\Users\UserSaved;
use System\Models\UserFavorite;
use System\Notifications\ResetPasswordNotification;
use System\Notifications\VerifyEmailNotification;
use System\Notifications\VerifyMobileNotification;
use System\Traits\Models\CategoryTrait;
use QCod\ImageUp\HasImageUploads;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;

class User extends Authenticatable implements JWTSubject, MustVerifyEmailContract
{
    use Notifiable, HasImageUploads, CategoryTrait;

    use MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'avatar', 'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = [
        'grade', 'avatar_url',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guard_name = 'web';

    /**
     * 模型的事件映射。
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => UserSaved::class,
        'deleted' => UserDeleted::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setImagesField([
            'image' => [
                'path' => uploads_path('user.avatar'),
                'disk' => config('filesystems.default', 'public'),
            ],
        ]);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /*
     * 获取头像为空返回默认头像
     */
    public function getAvatarAttribute($value)
    {
        return !empty($value) ? $value : config('params.users.default_avatar');
    }

    public function getAvatarUrlAttribute()
    {
        return asset_url($this->avatar);
    }

    /*
     * 存储密码前判断是否加密, 未加密则进行bcrypt()加密处理
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            // 如果值的长度等于 60，即认为是已经做过加密的情况
            if (strlen($value) !== 60) {
                // 不等于 60，做密码加密处理
                $value = bcrypt($value);
            }
            $this->attributes['password'] = $value;
        }
    }

    /*
     * 获取会员账户信息
     */
    public function account()
    {
        if (isset($this->id) && !UserAccount::query()->where('user_id', $this->id)->first()) {
            UserAccount::updateOrCreate(['user_id' => $this->id]);
        }

        return $this->hasOne(UserAccount::class, 'user_id', 'id')->withDefault(function ($account) {
            $account->user_id = $this->id;
            $account->money = 0;
            $account->freeze_money = 0;
            $account->spend_money = 0;
            $account->point = 0;
            $account->freeze_point = 0;
            $account->use_point = 0;
            $account->history_point = 0;
        });
    }

    public function extend()
    {
        if (isset($this->id) && !UserExtend::query()->where('user_id', $this->id)->first()) {
            UserExtend::updateOrCreate(['user_id' => $this->id]);
        }
        return $this->hasOne(UserExtend::class)->withDefault(function ($extend) {
            $extend->user_id = $this->id;
        });
    }

    /*
     * 提现账户
     */
    public function cashWithdrawalAccount()
    {
        return $this->hasOne(CashWithdrawalAccount::class)->withDefault(function ($item) {
            $item->id = 0;
            $item->user_id = $this->id;
        });
    }

    public function shop()
    {
        return $this->hasOne(Shop::class)->withDefault(function ($shop) {
            $shop->id = 0;
            $shop->user_id = $this->id;
        });
    }

    /*
     * 收货地址
     */
    public function address()
    {
        return $this->hasMany(UserAddress::class);
    }

    /*
     * 银行卡
     */
    public function cards()
    {
        return $this->hasMany(UserCard::class);
    }

    /*
     * 发票
     */
    public function invoices()
    {
        return $this->hasMany(UserInvoice::class);
    }

    /*
     * 收藏商品
     */
    public function getFavoriteProductsAttribute()
    {
        return UserFavorite::query()
            ->where('user_id', $this->id)
            ->where('favorite_id', UserFavorite::FAVORITE_TYPE_PRODUCT)
            ->get();
    }

    /*
     * 收藏文章
     */
    public function getFavoriteArticlesAttribute()
    {
        return UserFavorite::query()
            ->where('user_id', $this->id)
            ->where('favorite_id', UserFavorite::FAVORITE_TYPE_ARTICLE)
            ->get();
    }

    /*
     * 收藏店铺
     */
    public function getFavoriteShopsAttribute()
    {
        return UserFavorite::query()
            ->where('user_id', $this->id)
            ->where('favorite_id', UserFavorite::FAVORITE_TYPE_SHOP)
            ->get();
    }

    /*
     * 浏览商品
     */
    public function getBrowseProductsAttribute()
    {
        return UserBrowse::query()
            ->where('user_id', $this->id)
            ->where('browse_id', UserBrowse::BROWSE_TYPE_PRODUCT)
            ->get();
    }

    /*
     * 浏览文章
     */
    public function getBrowseArticlesAttribute()
    {
        return UserBrowse::query()
            ->where('user_id', $this->id)
            ->where('browse_id', UserBrowse::BROWSE_TYPE_ARTICLE)
            ->get();
    }

    /*
     * 浏览店铺
     */
    public function getBrowseShopsAttribute()
    {
        return UserBrowse::query()
            ->where('user_id', $this->id)
            ->where('browse_id', UserBrowse::BROWSE_TYPE_SHOP)
            ->get();
    }

    /*
     * 统计
     */
    public function getTotalCountAttribute()
    {
        $total_count = [
            'favorite_product' => $this->favorite_products->count(),
            'favorite_shop' => $this->favorite_shops->count(),
            'favorite_article' => $this->favorite_articles->count(),
            'browse_product' => $this->browse_products->count(),
            'browse_shop' => $this->browse_shops->count(),
            'browse_article' => $this->browse_articles->count(),
        ];
        return $total_count;
    }

    /*
     * 获取会员等级
     */
    public function getGradeAttribute()
    {
        $point = $this->account->history_point ?? 0;
        $user_grades = UserGrade::query()->active()->get();

        $grades = $user_grades->filter(function ($value, $key) use ($point) {
            return ($point >= $value->start_point && $point < $value->end_point);
        });

        if ($point > $user_grades->pluck('end_point')->max()) {
            return $user_grades->sortBy('end_point')->last();
        } else if (isset($grades) && count($grades->all())) {
            return $grades->first();
        } else {
            $grade = collect([
                'name' => trans('backend.commons.ordinary_user'),
                'level' => '1',
            ]);
            return $grade;
        }
    }

    /*
     * 判断邮箱是否验证
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    /*
     * 判断手机号是否验证
     */
    public function hasVerifiedMobile()
    {
        return !is_null($this->mobile_verified_at);
    }

    public function markMobileAsVerified()
    {
        return $this->forceFill([
            'mobile_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function sendMobileVerificationNotification()
    {
        $this->notify(new VerifyMobileNotification);
    }

    public function getMobileForVerification()
    {
        return $this->mobile;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}

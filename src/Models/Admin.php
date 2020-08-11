<?php

namespace System\Models;

use System\Traits\Models\CategoryTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use QCod\ImageUp\HasImageUploads;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles, Notifiable, HasImageUploads, CategoryTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'mobile', 'password', 'avatar', 'status',
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guard_name = 'admin';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setImagesField([
            'avatar' => [
                'width' => 200,
                'height' => 200,
                'rules' => 'image|max:300',
                'path' => uploads_path('user.avatar'),
                'disk' => config('filesystems.default', 'public'),
            ],
        ]);
    }

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

    public function getRolesNames()
    {
        $roles_names = $this->roles->pluck('title');
        return $roles_names->count() ? $roles_names->implode(config('systems.security.role_name_implode_symbol')) : trans('backend.commons.admin');
    }

    public function isSuperAdmin()
    {
        if ($this->id == 1 || $this->id == 1000 || $this->name == 'admin' || $this->hasrole(config('systems.security.administrator', 'Administrator'))) {
            return true;
        } else {
            return false;
        }
    }

    public function isAdmin()
    {
        return $this->status ? true : false;
    }
}

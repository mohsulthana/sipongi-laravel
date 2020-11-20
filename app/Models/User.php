<?php

namespace App\Models;

use App\Models\Spatie\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Avatar;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasApiTokens, HasRoles, LogsActivity;

    protected $tmpRole = null;
    protected $oldTmpRole = null;

    public $incrementing = false;

    protected $table = 'users';
    protected $keyType = 'string';
    protected $guard_name = 'api';

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'avatar',
        'is_super_admin',
        'status',
        'default_pass',
        'regional_id',
        'provinsi_id',
        'daops_id',
        'unit_kerja',
        'keterangan',
    ];

    protected static $logName = 'users';
    protected static $logAttributes = ['*', 'regional.nama_regional', 'provinsi.nama_provinsi'];
    protected static $logAttributesToIgnore = ['password', 'remember_token'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];
    protected $appends = ['avatar_url', 'role_name', 'role_name_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }

    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    public function isLogEmpty($attrs): bool
    {
        if (Arr::has($attrs, 'attributes')) {
            $attributes = Arr::get($attrs, 'attributes', []);
            if (Arr::has($attributes, 'updated_at')) {
                Arr::pull($attributes, 'updated_at');
            }
            Arr::set($attrs, 'attributes', $attributes);
        }
        if (Arr::has($attrs, 'old')) {
            $old = Arr::get($attrs, 'old', []);
            if (Arr::has($old, 'updated_at')) {
                Arr::pull($old, 'updated_at');
            }
            Arr::set($attrs, 'old', $old);
        }
        return empty($attrs['attributes'] ?? []) && empty($attrs['old'] ?? []);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $role_name = $this->tmpRole ? $this->tmpRole->display_name : null;
        $role_name_id = $this->tmpRole ? $this->tmpRole->name : null;
        $role_name_old = $this->oldTmpRole ? $this->oldTmpRole->display_name : null;
        $role_name_id_old = $this->oldTmpRole ? $this->oldTmpRole->name : null;

        if ($properties = $activity->properties) {
            if ($eventName === 'created') {
                if (Arr::has($properties, 'attributes')) {
                    $attributes = Arr::get($properties, 'attributes', []);
                    $attributes = array_merge($attributes, ['role_name' => $role_name]);
                    $attributes = array_merge($attributes, ['role_name_id' => $role_name_id]);
                    Arr::set($properties, 'attributes', $attributes);
                }
            } else if ($eventName === 'updated') {
                if ($role_name_id_old !== $role_name_id) {
                    if (Arr::has($properties, 'attributes')) {
                        $attributes = Arr::get($properties, 'attributes', []);
                        if (Arr::has($attributes, 'updated_at')) {
                            Arr::pull($attributes, 'updated_at');
                        }
                        $attributes = array_merge($attributes, ['role_name' => $role_name]);
                        $attributes = array_merge($attributes, ['role_name_id' => $role_name_id]);
                        Arr::set($properties, 'attributes', $attributes);
                    }
                    if (Arr::has($properties, 'old')) {
                        $old = Arr::get($properties, 'old', []);
                        if (Arr::has($old, 'updated_at')) {
                            Arr::pull($old, 'updated_at');
                        }
                        $old = array_merge($old, ['role_name' => $role_name_old]);
                        $old = array_merge($old, ['role_name_id' => $role_name_id_old]);
                        Arr::set($properties, 'old', $old);
                    }
                }

                if (Arr::has($properties, 'attributes')) {
                    $attributes = Arr::get($properties, 'attributes', []);
                    if (Arr::has($attributes, 'updated_at')) {
                        Arr::pull($attributes, 'updated_at');
                    }
                    Arr::set($properties, 'attributes', $attributes);
                }
                if (Arr::has($properties, 'old')) {
                    $old = Arr::get($properties, 'old', []);
                    if (Arr::has($old, 'updated_at')) {
                        Arr::pull($old, 'updated_at');
                    }
                    Arr::set($properties, 'old', $old);
                }
            } else {
                if (Arr::has($properties, 'attributes')) {
                    $attributes = Arr::get($properties, 'attributes', []);
                    $attributes = array_merge($attributes, ['role_name' => $this->role_name]);
                    $attributes = array_merge($attributes, ['role_name_id' => $this->role_name_id]);
                    Arr::set($properties, 'attributes', $attributes);
                }
            }
            $activity->properties = $properties;
        }
    }

    public function setTmpRoleAttribute($value)
    {
        if ($value) {
            $this->tmpRole = Role::findByName($value, $this->guard_name);
        }
    }

    public function setOldTmpRoleAttribute($value)
    {
        if ($value) {
            $this->oldTmpRole = $value;
        }
    }

    public function regional()
    {
        return $this->belongsTo(Regional::class, 'regional_id', 'id');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id');
    }

    public function getAvatarUrlAttribute()
    {
        $oldfileexists = Storage::disk('publicNas')->exists("avatars/$this->id/$this->avatar");
        if ($oldfileexists) {
            return array(
                'url' => Storage::disk('publicNas')->url("avatars/$this->id/$this->avatar"),
                'status' => 'url'
            );
        } else {
            $avatar = Avatar::create(strtoupper($this->name))->getImageObject()->encode('png');
            Storage::disk('publicNas')->put("avatars/$this->id/avatar.png", (string) $avatar);
            return array(
                'url' => Storage::disk('publicNas')->url("avatars/$this->id/$this->avatar"),
                'status' => 'url'
            );
        }
    }

    public function getRoleNameAttribute()
    {
        if ($this->is_super_admin) {
            return 'Super Admin';
        } else {
            $role = $this->roles->first();
            if ($role) {
                return $role->display_name;
            }

            return null;
        }
    }

    public function getRoleNameIdAttribute()
    {
        if ($this->is_super_admin) {
            return null;
        } else {
            $role = $this->roles->first();
            if ($role) {
                return $role->name;
            }

            return null;
        }
    }
}

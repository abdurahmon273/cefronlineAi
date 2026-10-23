<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Hash;
use App\Models\PremiumToUsers;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, HasFactory,HasApiTokens;

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    public const GENDER_SELECT = [
        '1' => 'Erkak',
        '0' => 'Ayol',
    ];

    protected $dates = [
        'email_verified_at',
        'birth_date',
        'mobile_verified_at',
        'login_token_expires_at',
        'login_token_used_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'surname',
        'chat_id',
        'email',
        'mobil_number',
        'email_verified_at',
        'email_verify_code',
        'password',
        'remember_token',
        'birth_date',
        'gender',
        'mobile_verify_code',
        'mobile_verified_at',
        'city_id',
        'image',
        'balance',
        'tmp_payment_slug',
        'tmp_payment_price',
        'tmp_payment_promocode',
        'tmp_payment_type',
        'created_by_id',
        'device_id',
        'session_id',
        'login_token',
        'login_token_expires_at',
        'login_token_used_at',
        'referral_expired_at',
        'current_referral_percentage',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }


    public function getIsExtraAdminAttribute()
    {
        return $this->roles()->where('title', 'extra_admin')->exists();
    }

    public function getIsWorkerAttribute()
    {
        return $this->roles()->where('title', 'worker')->exists();
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getBirthDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setBirthDateAttribute($value)
    {
        $this->attributes['birth_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getMobileVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setMobileVerifiedAtAttribute($value)
    {
        $this->attributes['mobile_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function qabul(): HasMany
    {
        return $this->hasMany(Qabul::class,'user_id');
    }
    public function videoWatched(): HasMany
    {
        return $this->hasMany(VideoWached::class, 'user_id');
    }
    public function savedLessons(): HasMany
    {
        return $this->hasMany(SavedLesson::class, 'user_id');
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'user_notification')->withPivot('read_at');
    }

    public function unreadNotificationsCount(): int
    {
        $userId = $this->id;

        return \App\Models\Notification::leftJoin('user_notification', function ($join) use ($userId) {
                $join->on('notifications.id', '=', 'user_notification.notification_id')
                     ->where('user_notification.user_id', $userId);
            })
            ->whereNull('user_notification.read_at')
            ->count();
    }

    public function activePremium(): ?PremiumToUsers
    {
        $today = now();

        return PremiumToUsers::where('user_id', $this->id)
            ->where('start_time', '<=', $today)
            ->where('end_time', '>=', $today)
            ->orderByDesc('id')
            ->first();
    }
    public function lastPremium(): ?PremiumToUsers
    {
        return PremiumToUsers::where('user_id', $this->id)
            ->orderByDesc('id')
            ->first();
    }
    public function firstPremium()
    {
        return $this->hasOne(PremiumToUsers::class, 'user_id')
            ->oldestOfMany(); // eng birinchi yozuv
    }

    public function balanceData():array
    {
        $globalSetting=GlobalSetting::orderByDesc('effective_at')->select('min_ai_credit_price','chat_gpt_tax','profit_tax','usd_current_currency')->firstOrFail();
        $minPrice=floatval(($globalSetting->min_ai_credit_price * ($globalSetting->usd_current_currency) / 100 *( ( $globalSetting->chat_gpt_tax + $globalSetting->profit_tax ) + 100 )) );
        $expired=true;

        if (floatval($this->balance) > floatval($minPrice) || $this->balance==0) {
            $expired=false;
        }

        return [
            'success'=>true,
            'expired'=>$expired,
            'balance'=>number_format($this->balance,0,'.',' ')
        ];
    }
    public function referral_users()
    {
       return $this->hasMany(User::class,'created_by_id','id');
    }

}

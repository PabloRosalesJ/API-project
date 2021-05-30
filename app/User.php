<?php

namespace App;

use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    const USER_VERIFIED = '1';
    const USER_NOT_VERIFIED = '0';

    const USER_ADMIN = 'true';
    const USER_NOT_ADMIN = 'false';

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
        //'admin',
        //'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'date:d-m-Y H:i:s',
        'updated_at' => 'date:d-m-Y H:i:s',
        'deleted_at' => 'date:d-m-Y H:i:s',
        'email_verified_at' => 'date:d-m-Y H:i:s'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user)
            {
                retry(3, function() use($user){
                    Mail::to($user)->send(new UserCreated($user));
                }, 1000);
            }
        );

        static::updating( function($user)
            {
                if ($user->isDirty('email'))
                {
                    retry(3, function() use ($user){
                        Mail::to($user)->send(new UserMailChanged($user));
                    }, 1000);
                }
            }
        );
    }

    public function setNameAttribute($attribute)
    {
        $this->attributes['name'] = strtolower($attribute);
    }

    public function getNameAttribute($attribute)
    {
        return ucwords($attribute);
    }

    public function setEmailAttribute($attribute)
    {
        $this->attributes['email'] = strtolower($attribute);
    }

    public function isVerified(){
        return $this->verified == User::USER_VERIFIED;
    }

    public function isAdmin()
    {
        return $this->admin == User::USER_ADMIN;
    }

    public static function generateVerificationToken()
    {
        return Str::random(40);
    }

}

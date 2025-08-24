<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
   
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const PROFILE_SUPER = 1;
    const PROFILE_ADMIN = 2;
    const PROFILES = [1=>'Super', 2=>'Admin', 3=>'User'];
    const PROFILES_SCOPES = [
        1 => ['Super', 'Admin', 'User'],
        2 => ['Admin', 'User'],
        3 => ['User'],
    ];

    ####################
    ### RELATIONSHIP ###


    ###############
    ### METHODS ###
    public function storeOrUpdate($formData)
    {
        if(!$this->id && $this->getUserByEmail($formData->email))
            throw new \Exception("Já existe usuário com email fornecido.");

        $this->name       = $formData->name;
        $this->email      = $formData->email;
        $this->password   = Hash::make(self::getStandardPassword());
        $this->profile_id = $formData->profile_id;
        $this->status_id  = @$formData->status_id ?? self::STATUS_ACTIVE;

        $this->save();

        return $this;
    }

    // public static function hasScope($scope)
    // {
    //     $user = Auth::user();
    //     $userScopes = User::PROFILES_SCOPES[$user->profile_id];
    //     if (in_array($scope, $userScopes)) {
    //         return true;
    //     }

    //     return false;
    // }


    // public static function hasProfile($profile)
    // {
    //     $user = Auth::user();

    //     return self::PROFILES[$user->profile_id] == $profile;
    // }


    // public static function getRoute($route)
    // {
    //     if(self::hasProfile('Retail'))
    //         return "$route-retail";
    //     else
    //         return "$route-adv";
    // }

    // AUX
    public function getUserByEmail($email)
    {
        return self::where('email', $email)->first();
    }


    public function profileName()
    {
        return self::PROFILES[$this->profile_id];
    }

    public static function getStandardPassword()
    {
        return 'crm'.date("dmY");
    }

}

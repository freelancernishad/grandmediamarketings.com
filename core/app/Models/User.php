<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'last_login' =>'datetime',
        'kyc_infos' => 'array'
    ];

    public function getFullNameAttribute($value)
    {
        return $this->fname.' '.$this->lname;
    }

    public function loginSecurity()
    {
        return $this->hasOne(LoginSecurity::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class,'user_id');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class,'user_id');
    }

    public function refferals()
    {
        return $this->hasMany(User::class,'reffered_by' );
    }

    public function refferedBy()
    {
        return $this->belongsTo(User::class,'reffered_by');
    }

    public function reffer()
    {
        return $this->hasMany(User::class,'reffered_by');
    }

    public function interest()
    {
        return $this->hasMany(UserInterest::class,'user_id');
    }

    public function commissions()
    {
        return $this->hasMany(RefferedCommission::class,'reffered_by');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class,'user_id');
    }

    public function getEmailAttribute($value)
    {
        return env('DEMO') ? '[Protected Email For Demo]' : $value;
    }

     public function getPhoneAttribute($value)
    {
        return env('DEMO') ? '[Protected Phone for Demo]' : $value;
    }


    public function currentDesignation()
    {
        return $this->hasOne(UserDesignation::class);
    }

    public function calculateTotalDeposit()
    {
        // Calculate the total deposit from the user
        $ownDeposit = Payment::where('user_id', $this->id)->sum('final_amount');

        // Calculate the total deposit from the user's referrals
        $referralDeposit = Payment::whereIn('user_id', $this->refferals->pluck('id'))->sum('final_amount');

        // Return the combined total deposit
        return $ownDeposit + $referralDeposit;
    }

    public function checkAndUpgradeDesignation()
    {
        $totalDeposit = $this->calculateTotalDeposit();
        $currentDesignation = $this->currentDesignation()->first();
        $designation = Designation::where('minimum_investment', '<=', $totalDeposit)
                                  ->orderBy('minimum_investment', 'desc')
                                  ->first();

        if ($designation && (!$currentDesignation || $currentDesignation->designation_id != $designation->id)) {
            UserDesignation::updateOrCreate(
                ['user_id' => $this->id],
                ['designation_id' => $designation->id, 'commission_level' => $designation->commission_level]
            );

            // Check if user already received the bonus for this designation
            if (!Transaction::where('user_id', $this->id)
                             ->where('details', 'Bonus for ' . $designation->name)
                             ->exists()) {
                Transaction::create([
                    'trx' => 'unique_transaction_id',
                    'user_id' => $this->id,
                    'amount' => $designation->bonus,
                    'currency' => 'USD',
                    'details' => 'Bonus for ' . $designation->name,
                    'type' => 'credit',
                    'payment_status' => 1,
                ]);
            }
        }
    }




}

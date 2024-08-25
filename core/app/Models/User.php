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
    protected $guarded = [];

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
        'last_login' => 'datetime',
        'kyc_infos' => 'array',
    ];

    public function getFullNameAttribute($value)
    {
        return $this->fname . ' ' . $this->lname;
    }

    public function loginSecurity()
    {
        return $this->hasOne(LoginSecurity::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'user_id');
    }

    public function refferals()
    {
        return $this->hasMany(User::class, 'reffered_by');
    }

    public function refferedBy()
    {
        return $this->belongsTo(User::class, 'reffered_by');
    }

    public function reffer()
    {
        return $this->hasMany(User::class, 'reffered_by');
    }

    public function interest()
    {
        return $this->hasMany(UserInterest::class, 'user_id');
    }

    public function commissions()
    {
        return $this->hasMany(RefferedCommission::class, 'reffered_by');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
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

    /**
     * Calculate the user's own total deposit.
     *
     * @return float
     */
    public function calculateTotalDeposit()
    {
        return Payment::where('user_id', $this->id)
        ->where('payment_status', 1)
            ->sum('final_amount');
    }

    /**
     * Calculate the total deposit of the user's team (referrals).
     *
     * @return float
     */
    public function calculateTeamDeposit()
    {
        return Payment::whereIn('user_id', $this->refferals->pluck('id'))
        ->where('payment_status', 1)
            ->sum('final_amount');
    }

    /**
     * Check if the user is eligible for a designation upgrade based on total deposits
     * and upgrade if applicable.
     *
     * @return void
     */

     public function checkAndUpgradeDesignation()
     {
         $totalOwnDeposit = $this->calculateTotalDeposit();
         $downlineDeposit = $this->calculateEntireDownlineDeposit();
         $totalCombinedDeposit = $totalOwnDeposit + $downlineDeposit;
 
         $currentDesignation = $this->currentDesignation()->first();
 
         $designation = Designation::where('minimum_investment', '<=', $totalCombinedDeposit)
             ->orderBy('minimum_investment', 'desc')
             ->first();
 
         if ($designation && (!$currentDesignation || $currentDesignation->designation_id != $designation->id)) {
             UserDesignation::updateOrCreate(
                 ['user_id' => $this->id],
                 [
                     'designation_id' => $designation->id,
                     'commission_level' => $designation->commission_level
                 ]
             );
 
             $this->promoteReferredByUser();
 
             if (!Transaction::where('user_id', $this->id)
                 ->where('details', 'Bonus for ' . $designation->id)
                 ->exists()) {
 
                 $transaction = Transaction::create([
                     'trx' => uniqid('trx_'),
                     'user_id' => $this->id,
                     'gateway_id' => 0,
                     'amount' => $designation->bonus,
                     'charge' => 0,
                     'currency' => 'USD',
                     'details' => 'Bonus for ' . $designation->id,
                     'type' => 'credit',
                     'payment_status' => 1,
                 ]);
 
                 $this->increment('balance', $transaction->amount);
             }
         }
     }
 
     protected function promoteReferredByUser()
     {
         $referredBy = $this->referredBy;
 
         while ($referredBy) {
             $referrerTotalDeposit = $referredBy->calculateTotalDeposit();
             $referrerTeamDeposit = $this->calculateDepositFromEntireDownline($referredBy);
             $referrerCombinedDeposit = $referrerTotalDeposit + $referrerTeamDeposit;
 
             $currentReferrerDesignation = $referredBy->currentDesignation()->first();
 
             $referrerDesignation = Designation::where('minimum_investment', '<=', $referrerCombinedDeposit)
                 ->orderBy('minimum_investment', 'desc')
                 ->first();
 
             if ($referrerDesignation) {
                 if (!$currentReferrerDesignation || $currentReferrerDesignation->designation_id != $referrerDesignation->id) {
                     UserDesignation::updateOrCreate(
                         ['user_id' => $referredBy->id],
                         [
                             'designation_id' => $referrerDesignation->id,
                             'commission_level' => $referrerDesignation->commission_level
                         ]
                     );
 
                     if (!Transaction::where('user_id', $referredBy->id)
                         ->where('details', 'Bonus for ' . $referrerDesignation->id)
                         ->exists()) {
 
                         $transaction = Transaction::create([
                             'trx' => uniqid('trx_'),
                             'user_id' => $referredBy->id,
                             'gateway_id' => 0,
                             'amount' => $referrerDesignation->bonus,
                             'charge' => 0,
                             'currency' => 'USD',
                             'details' => 'Bonus for ' . $referrerDesignation->id,
                             'type' => 'credit',
                             'payment_status' => 1,
                         ]);
 
                         $referredBy->increment('balance', $transaction->amount);
                     }
                 }
 
                 break;
             }
 
             $referredBy = $referredBy->referredBy;
         }
     }
 
     public function calculateEntireDownlineDeposit()
     {
         $totalDeposit = 0;
         
         $referrals = $this->referrals()->get(); // Ensure this returns a collection
 
         foreach ($referrals as $referral) {
             $totalDeposit += $referral->calculateTotalDeposit();
             $totalDeposit += $referral->calculateEntireDownlineDeposit();
         }
 
         return $totalDeposit;
     }
 
     protected function calculateDepositFromEntireDownline($user)
     {
         $totalDeposit = 0;
 
         $referrals = $user->referrals()->get(); // Ensure this returns a collection
 
         foreach ($referrals as $referral) {
             $totalDeposit += $referral->calculateTotalDeposit();
             $totalDeposit += $referral->calculateEntireDownlineDeposit();
         }
 
         return $totalDeposit;
     }










}

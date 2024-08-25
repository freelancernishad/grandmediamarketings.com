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
        $totalDeposit = $this->calculateTotalDeposit(); // User's own deposit
        $teamDeposit = $this->calculateTeamDeposit(); // User's team deposit
        $totalCombinedDeposit = $totalDeposit + $teamDeposit; // Combined deposit
        $currentDesignation = $this->currentDesignation()->first();

        // Find the highest eligible designation based on the total combined deposit
        $designation = Designation::where('minimum_investment', '<=', $totalCombinedDeposit)
            ->orderBy('minimum_investment', 'desc')
            ->first();

        if ($designation && (!$currentDesignation || $currentDesignation->designation_id != $designation->id)) {
            // Upgrade the user's designation
            UserDesignation::updateOrCreate(
                ['user_id' => $this->id],
                [
                    'designation_id' => $designation->id,
                    'commission_level' => $designation->commission_level
                ]
            );

           
   
            // Check if the user has already received the bonus for this designation
            if (!Transaction::where('user_id', $this->id)
                ->where('details', 'Bonus for ' . $designation->id)
                ->exists()) {
                // Create a transaction for the bonus
                Transaction::create([
                    'trx' => uniqid('trx_'), // Unique transaction ID
                    'user_id' => $this->id,
                    'gateway_id' => 0,
                    'amount' => $designation->bonus,
                    'charge' => 0,
                    'currency' => 'USD',
                    'details' => 'Bonus for ' . $designation->id,
                    'type' => 'credit',
                    'payment_status' => 1,
                ]);
            }
        }
    }
}

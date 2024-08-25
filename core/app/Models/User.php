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
        // User's own deposit
        $totalOwnDeposit = $this->calculateTotalDeposit();

        // User's downline deposit, including deposits from all referred users (direct or indirect)
        $downlineDeposit = $this->calculateEntireDownlineDeposit();

        // Combined deposit (user's deposit + downline deposit)
        $totalCombinedDeposit = $totalOwnDeposit + $downlineDeposit;

        // Fetch the user's current designation
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

            // Promote the user who referred the current user (if applicable)
            $this->promoteReferredByUser();

            // Check if the user has already received the bonus for this designation
            if (!Transaction::where('user_id', $this->id)
                ->where('details', 'Bonus for ' . $designation->id)
                ->exists()) {

                // Create a transaction for the bonus
                $transaction = Transaction::create([
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

                // Update the user's balance
                $this->increment('balance', $transaction->amount);
            }
        }
    }

    protected function promoteReferredByUser()
    {
        // Initialize the current referrer to be the user who referred the current user
        $referredBy = $this->referredBy;

        // Traverse up the referral chain until you find a referrer who has invested
        while ($referredBy) {
            // Calculate the referrer's total deposit
            $referrerTotalDeposit = $referredBy->calculateTotalDeposit();

            // Calculate the referrer's team deposit, including the entire downline
            $referrerTeamDeposit = $this->calculateDepositFromEntireDownline($referredBy);

            // Calculate the referrer's combined deposit
            $referrerCombinedDeposit = $referrerTotalDeposit + $referrerTeamDeposit;

            // Fetch the referrer's current designation
            $currentReferrerDesignation = $referredBy->currentDesignation()->first();

            // Find the highest eligible designation for the referrer based on the combined deposit
            $referrerDesignation = Designation::where('minimum_investment', '<=', $referrerCombinedDeposit)
                ->orderBy('minimum_investment', 'desc')
                ->first();

            if ($referrerDesignation) {
                // Upgrade the referrer's designation if needed
                if (!$currentReferrerDesignation || $currentReferrerDesignation->designation_id != $referrerDesignation->id) {
                    UserDesignation::updateOrCreate(
                        ['user_id' => $referredBy->id],
                        [
                            'designation_id' => $referrerDesignation->id,
                            'commission_level' => $referrerDesignation->commission_level
                        ]
                    );

                    // Check if the referrer has already received the bonus for this designation
                    if (!Transaction::where('user_id', $referredBy->id)
                        ->where('details', 'Bonus for ' . $referrerDesignation->id)
                        ->exists()) {

                        // Create a transaction for the bonus
                        $transaction = Transaction::create([
                            'trx' => uniqid('trx_'), // Unique transaction ID
                            'user_id' => $referredBy->id,
                            'gateway_id' => 0,
                            'amount' => $referrerDesignation->bonus,
                            'charge' => 0,
                            'currency' => 'USD',
                            'details' => 'Bonus for ' . $referrerDesignation->id,
                            'type' => 'credit',
                            'payment_status' => 1,
                        ]);

                        // Update the referrer's balance
                        $referredBy->increment('balance', $transaction->amount);
                    }
                }

                // Break out of the loop once an eligible referrer has been found and promoted
                break;
            }

            // Move to the next referrer up the chain if the current referrer has not invested
            $referredBy = $referredBy->referredBy;
        }
    }

    /**
     * Calculate the total deposit from the entire downline of the given user.
     * This includes investments made by users referred directly and indirectly.
     *
     * @param User $user
     * @return float
     */
    protected function calculateDepositFromEntireDownline(User $user)
    {
        $totalDeposit = 0;

        foreach ($user->referrals as $referral) {
            $totalDeposit += $referral->calculateTotalDeposit();
            $totalDeposit += $this->calculateDepositFromEntireDownline($referral);
        }

        return $totalDeposit;
    }











}

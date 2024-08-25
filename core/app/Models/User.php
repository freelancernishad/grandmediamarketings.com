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

    /**
     * Get the full name of the user.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->fname . ' ' . $this->lname;
    }

    /**
     * Get the login security associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function loginSecurity()
    {
        return $this->hasOne(LoginSecurity::class);
    }

    /**
     * Get the payments associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    /**
     * Get the deposits associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'user_id');
    }

    /**
     * Get the referrals for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function refferals()
    {
        return $this->hasMany(User::class, 'reffered_by');
    }

    /**
     * Get the user who referred this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function refferedBy()
    {
        return $this->belongsTo(User::class, 'reffered_by');
    }

    /**
     * Get the users referred by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reffer()
    {
        return $this->hasMany(User::class, 'reffered_by');
    }

    /**
     * Get the interests associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function interest()
    {
        return $this->hasMany(UserInterest::class, 'user_id');
    }

    /**
     * Get the commissions for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commissions()
    {
        return $this->hasMany(RefferedCommission::class, 'reffered_by');
    }

    /**
     * Get the tickets associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Get the email attribute, protected in demo mode.
     *
     * @return string
     */
    public function getEmailAttribute($value)
    {
        return env('DEMO') ? '[Protected Email For Demo]' : $value;
    }

    /**
     * Get the phone attribute, protected in demo mode.
     *
     * @return string
     */
    public function getPhoneAttribute($value)
    {
        return env('DEMO') ? '[Protected Phone for Demo]' : $value;
    }

    /**
     * Get the current designation of the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
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

        // User's team deposit, including deposits from referred users
        $teamDeposit = $this->calculateTeamDeposit();

        // Combined deposit (user's deposit + team deposit)
        $totalCombinedDeposit = $totalOwnDeposit + $teamDeposit;

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

    /**
     * Promote the referrer or traverse the referral chain up until a user with an investment is found.
     *
     * @return void
     */
    protected function promoteReferredByUser()
    {
        // Start with the current user and traverse up the referral chain
        $currentUser = $this;
        while ($currentUser) {
            // Calculate the current user's total and team deposits
            $totalDeposit = $currentUser->calculateTotalDeposit();
            $teamDeposit = $currentUser->calculateTeamDeposit();

            // Only proceed if the user has made an investment
            if ($totalDeposit > 0) {
                // Calculate the user's combined deposit
                $combinedDeposit = $totalDeposit + $teamDeposit;

                // Fetch the user's current designation
                $currentDesignation = $currentUser->currentDesignation()->first();

                // Find the highest eligible designation for the user based on the combined deposit
                $designation = Designation::where('minimum_investment', '<=', $combinedDeposit)
                    ->orderBy('minimum_investment', 'desc')
                    ->first();

                if ($designation && (!$currentDesignation || $currentDesignation->designation_id != $designation->id)) {
                    // Upgrade the user's designation
                    UserDesignation::updateOrCreate(
                        ['user_id' => $currentUser->id],
                        [
                            'designation_id' => $designation->id,
                            'commission_level' => $designation->commission_level
                        ]
                    );

                    // Check if the user has already received the bonus for this designation
                    if (!Transaction::where('user_id', $currentUser->id)
                        ->where('details', 'Bonus for ' . $designation->id)
                        ->exists()) {

                        // Create a transaction for the bonus
                        $transaction = Transaction::create([
                            'trx' => uniqid('trx_'), // Unique transaction ID
                            'user_id' => $currentUser->id,
                            'gateway_id' => 0,
                            'amount' => $designation->bonus,
                            'charge' => 0,
                            'currency' => 'USD',
                            'details' => 'Bonus for ' . $designation->id,
                            'type' => 'credit',
                            'payment_status' => 1,
                        ]);

                        // Update the user's balance
                        $currentUser->increment('balance', $transaction->amount);
                    }
                }

                // Break the loop once a user has been promoted
                break;
            }

            // Move to the next referrer in the chain
            $currentUser = $currentUser->refferedBy;
        }
    }
}

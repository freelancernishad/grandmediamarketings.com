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
     * Get the user's current designation details.
     *
     * @return \App\Models\Designation|null
     */
    public function designation()
    {
        // Fetch the current designation relationship
        $currentDesignation = $this->currentDesignation()->first();

        // Return the designation if it exists, otherwise return null
        return $currentDesignation ? $currentDesignation->designation : null;
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
            ->sum('amount');
    }

    /**
     * Calculate the total deposit of the user's team (referrals).
     *
     * @return float
     */
    public function calculateTeamDeposit()
    {
        // Initialize the total deposit
        $totalDeposit = 0;

        // Get all direct referrals of the current user
        $referrals = $this->refferals;

        // Use a queue for breadth-first traversal of the referral tree
        $queue = $referrals->pluck('id')->toArray(); // Initialize the queue with direct referrals

        // Track visited users to avoid processing the same user multiple times
        $visited = [];

        while (!empty($queue)) {
            $currentUserId = array_shift($queue); // Dequeue the first user ID
            $currentUser = User::find($currentUserId); // Find the current user

            if (!$currentUser || in_array($currentUserId, $visited)) {
                continue; // Skip if user is not found or already visited
            }

            $visited[] = $currentUserId; // Mark user as visited

            // Accumulate deposits for the current user
            $userDeposit = Payment::where('user_id', $currentUserId)
                ->where('payment_status', 1)
                ->sum('amount');
            $totalDeposit += $userDeposit;

            // Debug: Output the current user's deposits and total so far
            // This can be logged or displayed depending on your environment
            // For debugging in Laravel, you might use Log::info() or dd()
            // \Log::info("User ID: $currentUserId, Deposit: $userDeposit, Total Deposit: $totalDeposit");

            // Enqueue all referrals of the current user
            $referrals = $currentUser->refferals;
            foreach ($referrals as $referral) {
                if (!in_array($referral->id, $visited)) {
                    $queue[] = $referral->id;
                }
            }
        }

        // Debug: Output the final total deposit
        // \Log::info("Final Total Deposit: $totalDeposit");

        return $totalDeposit;
    }



    /**
     * Check if the user is eligible for a designation upgrade based on total deposits
     * and upgrade if applicable.
     *
     * @return void
     */
    public function checkAndUpgradeDesignation()
    {


    //  return   $this->promoteReferredByUser();
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


              // Promote the user who referred the current user (if applicable)
               $this->promoteReferredByUser();

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
        // Start with the user who referred the current user
        $currentUser = $this->refferedBy;

        // Create a set to track visited users and avoid loops
        $visitedUsers = [];

        // Loop through the referral chain
        while ($currentUser) {
            // Check if we've already visited this user to avoid cycles
            if (in_array($currentUser->id, $visitedUsers)) {
                break; // Exit the loop if a cycle is detected
            }

            // Add the current user to the visited users set
            $visitedUsers[] = $currentUser->id;

            // Calculate the current user's total and team deposits
            $totalDeposit = $currentUser->calculateTotalDeposit();
            $teamDeposit = $currentUser->calculateTeamDeposit();

            // Only proceed if the referrer has made an investment
            if ($totalDeposit > 0) {
                // Calculate the user's combined deposit
                $combinedDeposit = $totalDeposit + $teamDeposit;

                // Fetch the user's current designation
                $currentDesignation = $currentUser->currentDesignation()->first();

                // Find the highest eligible designation for the referrer based on the combined deposit
                $designation = Designation::where('minimum_investment', '<=', $combinedDeposit)
                    ->orderBy('minimum_investment', 'desc')
                    ->first();

                if ($designation && (!$currentDesignation || $currentDesignation->designation_id != $designation->id)) {
                    // Upgrade the referrer's designation
                    UserDesignation::updateOrCreate(
                        ['user_id' => $currentUser->id],
                        [
                            'designation_id' => $designation->id,
                            'commission_level' => $designation->commission_level
                        ]
                    );

                    // Check if the referrer has already received the bonus for this designation
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

                        // Update the referrer's balance
                        $currentUser->increment('balance', $transaction->amount);
                    }

                    // Continue to find the next referrer who might need promotion
                    // Skip the "break;" here to continue checking the referral chain
                }
            }

            // Move to the next referrer in the chain
            $currentUser = $currentUser->refferedBy;
        }
    }

}

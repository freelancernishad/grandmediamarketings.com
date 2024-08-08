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
          $bonusAmount = floatval($designation->bonus);


        if ($designation && (!$currentDesignation || $currentDesignation->designation_id != $designation->id)) {
            // Upgrade designation
            UserDesignation::updateOrCreate(
                ['user_id' => $this->id],
                ['designation_id' => $designation->id]
            );



            // Check if the user has already received the bonus for this designation
            $bonusReceived = Transaction::where('user_id', $this->id)
                                        ->where('type', 'bonus')
                                        ->where('details', 'like', "%Designation ID: {$designation->id}%")
                                        ->exists();

            if (!$bonusReceived && $bonusAmount > 0) {

                // Create a new transaction for the bonus
                Transaction::create([
                    'trx' => uniqid(),  // Generate a unique transaction ID
                    'gateway_transaction' => null,
                    'user_id' => $this->id,
                    'gateway_id' => 0,  // Assuming no gateway is involved in bonus
                    'amount' => $bonusAmount,
                    'currency' => 'USD',  // Adjust currency as needed
                    'charge' => 0,  // Assuming no charge for bonus
                    'details' => "Designation ID: {$designation->id} Bonus",
                    'type' => 'bonus',
                    'payment_status' => 1,  // Assuming bonus payment is successful
                ]);
            }
        }
    }



}

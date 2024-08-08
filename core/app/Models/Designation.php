<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'minimum_investment',
        'bonus',
    ];

    public function userDesignations()
    {
        return $this->hasMany(UserDesignation::class);
    }
}

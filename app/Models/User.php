<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'eth_address',
        'email',
        'twitter',
        'telegram',
        'domain',
    ];

    protected $hidden = [
        'id',
        'eth_address',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'address'
    ];

    public function getAddressAttribute(){
        return $this->attributes['eth_address'];
    }
}

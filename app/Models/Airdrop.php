<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airdrop extends Model
{
    protected $fillable = [
        'eth_address',
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

    public static function findByAddress($address) {
        return self::where('LOWER(eth_address) = ?', strtolower($address))->first();
    }
}

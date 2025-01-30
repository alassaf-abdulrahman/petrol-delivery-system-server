<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'paymentID';
    public $timestamps = false;

    protected $fillable = [
        'orderID',
        'amount',
        'paymentMethod',
        'status',
        'cardNumber',
        'expiryDate',
        'cvv',
        'address',
        'zipCode',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'orderID');
    }
}

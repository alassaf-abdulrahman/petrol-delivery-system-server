<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'orderID';
    public $timestamps = false;

    protected $fillable = [
        'customerID',
        'driverID',
        'fuelType',
        'quantity',
        'status',
        'deliveryLocation',
        'orderTime',
        'amount',
        'orderDate'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerID');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driverID');
    }

    public function receipts()
    {
        return $this->hasOne(Receipt::class, 'orderID');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'orderID');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'receipts';
    protected $primaryKey = 'receiptID';
    public $timestamps = false;

    protected $fillable = ['orderID', 'amount'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'orderID');
    }
}

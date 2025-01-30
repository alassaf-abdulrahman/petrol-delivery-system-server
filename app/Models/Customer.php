<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'customerID';
    public $timestamps = false;

    protected $fillable = ['userID', 'name'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customerID');
    }
}

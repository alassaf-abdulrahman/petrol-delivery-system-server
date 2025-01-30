<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers';
    protected $primaryKey = 'driverID';
    public $timestamps = false;

    protected $fillable = ['userID', 'name', 'licenseNumber'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    public function trucks()
    {
        return $this->hasMany(Truck::class, 'driverID');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'driverID');
    }
}

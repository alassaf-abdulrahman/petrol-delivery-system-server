<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    use HasFactory;

    protected $table = 'trucks';
    protected $primaryKey = 'truckID';
    public $timestamps = false;

    protected $fillable = ['driverID', 'licensePlate', 'safetyCertified'];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driverID');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'userID';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'password',
        'name',
        'role',
        'phoneNumber',
    ];

    public function admin()
    {
        return $this->hasOne(Admin::class, 'userID');
    }

    public function driver()
    {
        return $this->hasOne(Driver::class, 'userID');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'userID');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admins';
    protected $primaryKey = 'adminID';
    public $timestamps = false;

    protected $fillable = ['userID'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }
}

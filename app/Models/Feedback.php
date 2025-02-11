<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    //
    protected $table = "feedbacks";
    protected $primaryKey = 'feedbackID';
    protected $fillable = ["userID", "feedback", "submittedBy"];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, "userID");
    }
}

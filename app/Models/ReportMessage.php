<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportMessage extends Model
{
    protected $fillable = ['user_id', 'body'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

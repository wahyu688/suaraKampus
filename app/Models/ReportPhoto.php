<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportPhoto extends Model
{
    protected $fillable = ['path'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}

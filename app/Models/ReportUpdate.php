<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportUpdate extends Model
{
    protected $fillable = ['user_id', 'status', 'title', 'note'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // Petugas yang membuat perubahan status
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

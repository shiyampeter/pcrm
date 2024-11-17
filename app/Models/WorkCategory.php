<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkCategory extends Model
{
    protected $table = 'work_category';
    protected $primaryKey = 'work_id';

    protected $fillable = [
        'work_name',
        'work_type',
        'work_tracking_no',
        'work_tracking_website',
        'work_completed',
    ];
    public $timestamps = false;
}

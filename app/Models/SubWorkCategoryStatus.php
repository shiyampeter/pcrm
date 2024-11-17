<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubWorkCategoryStatus extends Model
{
    protected $table = 'sub_work_category_status';

    protected $fillable = [
        'status',
    ];
    public $timestamps = false;
}

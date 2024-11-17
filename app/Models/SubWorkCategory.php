<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubWorkCategory extends Model
{
    protected $table = 'sub_work_category';
    protected $primaryKey = 'sub_work_id';
    protected $casts = [
        'sub_work_work_price' => 'array',
        'sub_work_online_price' => 'array',
        'sub_work_expense_price' => 'array',
        'sub_work_discount_price' => 'array',
        'sub_work_incentive_price' => 'array',
        'sub_work_validity' => 'array',
    ];
    protected $fillable = [
        'sub_work_cate_id',
        'sub_work_cate_name',
        'sub_work_work_price',
        'sub_work_online_price',
        'sub_work_expense_price',
        'sub_work_discount_price',
        'sub_work_incentive_price',
        'sub_work_validity_status',
        'sub_work_validity',
        'sub_work_validity_date',
        'sub_work_alert_status',
        'sub_work_alert_days_type',
        'sub_work_alert_days',
        'status_id',
    ];
    public $timestamps = false;
}

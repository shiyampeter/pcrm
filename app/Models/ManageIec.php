<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageIec extends Model
{

    protected $table = 'manage_iec';
    protected $primaryKey = 'iec_q_id';
    protected $casts = [
        'sub_work_work_price' => 'array',
        'sub_work_online_price' => 'array',
        'sub_work_expense_price' => 'array',
        'sub_work_discount_price' => 'array',
        'sub_work_incentive_price' => 'array',
        'sub_work_validity' => 'array',
    ];
    protected $fillable = [
        'iec_q_ref_no',
        'iec_q_ack_no',
        'iec_q_name',
        'iec_q_mobile',
        'iec_q_work',
        'iec_q_as_name',
        'iec_q_sub_work',
        'iec_q_location',
        'iec_q_feedback',
        'iec_q_amount',
        'iec_q_discount',
        'iec_q_online_payment',
        'iec_online_payment',
        'iec_online_payment_gothrough',
        'iec_q_adv_amount',
        'iec_q_expense',
        'iec_q_office_expense',
        'iec_q_income',
        'iec_paid',
        'iec_date_of_pay',
        'iec_q_work_type',
        'iec_complete',
        'iec_remarks',
        'iec_validity',
        'iec_validity_eb',
        'iec_q_added_on_d',
        'iec_q_added_by',
        'iec_q_added_on',
        'iec_q_added_ip',
        'iec_q_modified_by',
        'iec_q_modified_on',
        'iec_q_modified_ip',
        'iec_q_deleted_by',
        'iec_q_deleted_on',
        'iec_q_deleted_ip',
        'iec_q_del_status',
        'iec_q_alert_date',
        'iec_q_attach',
        'iec_inform',
        'iec_o_complete',
        'iec_payment_mode',
        'iec_wallet_amt ',
        'iec_q_re_wallet_amt',
        'iec_completed_by',
    ];
    public $timestamps = false;
    public function subWork()
    {
        return $this->hasOne(SubWorkCategory::class, 'sub_work_id', 'iec_q_sub_work');
    }
    public function work()
    {
        return $this->hasOne(WorkCategory::class, 'work_id', 'iec_q_work');
    }
}



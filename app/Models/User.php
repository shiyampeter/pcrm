<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_uniq_id',
        'user_username',
        'user_password',
        'user_new_pass',
        'user_level',
        'user_caller',
        'user_master_id',
        'user_title',
        'user_mail',
        'user_dob',
        'user_dob_y',
        'user_dob_m_d',
        'user_phone_no',
        'user_url',
        'user_calling_language',
        'user_calling_option',
        'user_calling_type',
        'user_calling_status',
        'user_calling_temp_status',
        'user_calling_dnd',
        'user_calling_auto_call_status',
        'user_app_notification_token',
        'user_last_login',
        'user_login_count',
        'user_group',
        'user_accessibilities',
        'user_team_leader_id',
        'user_manager_id',
        'user_process',
        'user_designation',
        'user_employee_id',
        'user_salary',
        'user_branch_id',
        'user_company_id',
        'user_active_status',
        'user_aproval_status',
        'user_aproval_time',
        'user_offer_id',
        'user_leads_arr_check_box',
        'user_online_status',
        'user_live_status',
        'user_live_time',
        'user_added_by',
        'user_added_on',
        'user_added_ip',
        'user_modified_details',
        'user_deleted_by',
        'user_deleted_on',
        'user_deleted_ip',
        'user_actv_inactv',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}

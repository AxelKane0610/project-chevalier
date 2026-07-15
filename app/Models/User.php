<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'fullname',
        'learner_id',
        'site_id',
        'roles',
        'leader_id',
        'phone_number',
        'team'
        
    ];

    protected $casts = [
        'roles' => 'array', // Ép kiểu từ chuỗi JSON trong DB thành mảng PHP
    ];

    public function hasRole($role) // Hàm kiểm tra xem user có role nào đó không, để hiện hoặc ẩn nút action trên view
    {
    // Kiểm tra xem cái role cần tìm có nằm trong mảng roles của User không
        
        return in_array($role, $this->roles ?? []);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

    public function getSiteIdDataAttribute()
    {
        return match ($this->site_id){
            1 => [
                'text' => 'Hồ Chí Minh',
                'class' => 'open'
            ],

            2 => [
                'text' => 'Hà Nội',
                'class' => 'waiting-for-verifier'
            ],

            3 => [
                'text' => 'Đà Nẵng',
                'class' => 'waiting-for-approver'
            ],

            4 => [
                'text' => 'Cần Thơ',
                'class' => 'completed'
            ],

            5 => [
                'text' => 'Call Center',
                'class' => 'rejected'
            ],



            default => [
                'text' => 'Unknown',
                'class' => 'unknown'
            ]
        };
    }

    public function getTeamDataAttribute()
    {
        // dd($this->team);
        return match ((int) $this->team){
            1 => [
                'text' => 'ASRC',
                'class' => 'open'
            ],

            2 => [
                'text' => 'Call Center',
                'class' => 'waiting-for-verifier'
            ],

            3 => [
                'text' => 'Customer Engineer',
                'class' => 'waiting-for-approver'
            ],

            4 => [
                'text' => 'Front Counter',
                'class' => 'completed'
            ],

            5 => [
                'text' => 'Call Admin',
                'class' => 'rejected'
            ],

            6 => [
                'text' => 'Operation',
                'class' => 'rejected'
            ],



            default => [
                'text' => 'Unknown',
                'class' => 'unknown'
            ]
        };
    }

    
}

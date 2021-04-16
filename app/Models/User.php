<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the icons for the user.
     */
    public function icons()
    {
        return $this->hasMany(Icon::class, 'contributor_id');
    }

    public function team_member()
    {
        if ($this->type == 'team-member') {
            return $this->hasOne(TeamMember::class)->withDefault();
        } else if ($this->type == 'team-admin') {
            return $this->hasOne(TeamAdmin::class)->withDefault();
        }
    }

    public function team()
    {
        return $this->team_member->team;
    }

}

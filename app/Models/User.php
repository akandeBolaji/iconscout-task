<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

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
        'type'
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

    public function roll_api_key()
    {
        do {
            $token = base64_encode( hash('sha256',time()) . hash('sha256',getenv('APP_KEY')) . random_bytes(206) );
            $this->api_token = $token;
        } while( $this->where('api_token', $this->api_token)->exists() );
        $this->api_token_expire_at = Carbon::now()->addDays(30);
        $this->save();
    }

}

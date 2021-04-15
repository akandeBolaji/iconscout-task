<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function members()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function admins()
    {
        return $this->hasMany(TeamAdmin::class);
    }
}

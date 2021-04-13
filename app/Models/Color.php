<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hex_value',
        'hsl_value',
        'dec_value',
        'name'
    ];

    /**
     * The icons that belong to the tag.
     */
    public function icons()
    {
        return $this->belongsToMany(Icon::class);
    }
}

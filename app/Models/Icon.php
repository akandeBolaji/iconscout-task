<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Icon extends Model
{
    use HasFactory;
        /**
     * Table name associated with
     *
     * @var $table
     */
    protected $table = 'icons';

    /**
     * Specify primary key
     *
     * @var $primaryKey
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'img_url',
        'price',
        'style',
        'contributor_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Get the user that owns the phone.
     */
    public function contributor()
    {
        return $this->belongsTo(User::class, 'contributor_id');
    }

    /**
     * The colors that belong to the icon.
     */
    public function colors()
    {
        return $this->belongsToMany(Color::class);
    }

    /**
     * The categories that belong to the icon.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * The tags that belong to the icon.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}

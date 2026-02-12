<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workplace extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'workplaces';

    protected $fillable = [
        'employer',
        'CUI',
        'reg_com',
        'adresa',
        'oras',
        'judet',

    ];

    public function members()
    {
        return $this->belongsToMany(Member::class, 'member_workplace')->withTimestamps();
    }
}

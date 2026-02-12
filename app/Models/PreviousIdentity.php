<?php

namespace App\Models;

use Filament\Forms\Components\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreviousIdentity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'previous_identities';

    protected $fillable = [
        'member_id',
        'ci_serie',
        'ci_numar',
        'emis_de',
        'data_emitere',
        'data_expirare',
        'nume',
        'prenume',
        'cetatenie',
        'nationalitate',
        'domiciliu',
        'oras',
        'judet',
        'oras_nastere',
        'judet_nastere',
        'scan_carte_identitate',
        'user_id',
    ];

    protected $appends = [
        'full_name',
        'CI',
    ];

    public function getFullNameAttribute()
    {
        return $this->nume . ' ' . $this->prenume;

    }

    public function scopeFilterByFullName(Builder $query, $search)
    {
        return $query->where('full_name', 'like', "%{$search}%");
    }


    public function getCIAttribute()
    {
        return $this->ci_serie . ' ' . $this->ci_numar;

    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}

<?php

namespace App\Models;

use Filament\Forms\Components\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'CNP',
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
        'contact_info',
        'workplace',
        'alte_info',
        'scan_carte_identitate',
        'user_id',
    ];

    protected $appends = [
        'full_name',
        'CI',

    ];

    protected $casts = [
        'contact_info' => 'json',
        'workplace' => 'json',
        'alte_info' => 'json',
    ];

    public function rules()
    {
        return [
            'CNP' => [
                'required',
                'size:13',
                'unique:members,CNP',

                'regex:/^[1-9][0-9]{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12][0-9]|3[01])(?:0[1-9]|[1-4][0-9]|5[0-2]|99)(?:00[1-9]|0[1-9][0-9]|[1-9][0-9]{2})[0-9]{3}$/',

                'valid_cnp' => function ($attribute, $value, $fail) {
                    $digits = str_split($value);

                    $sum = 0;
                    for ($i = 0; $i < 12; $i++) {
                        $digit = $digits[$i];
                        $sum += $digit * (13 - $i);
                    }

                    $check = $sum % 11;
                    if ($check == 10) {
                        $check = 1;
                    }

                    if ($check != $digits[12]) {
                        $fail('Invalid CNP check digit.');
                    }
                },
            ],
            'ci_serie' => [
                'required',
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/',
            ],
            'ci_numar' => [
                'required',
                'digits:6',
            ],
        ];
    }

    public function validationMessages()
    {
        return [
            'CNP.size'   => 'CNP must be 13 digits',
            'CNP.unique' => 'CNP already exists',
            'CNP.regex'  => 'Invalid CNP format',
            'ci_serie.size'   => 'CI serie must be exactly 2 letters',
            'ci_serie.regex'  => 'CI serie must contain only uppercase letters (A-Z)',
            'ci_numar.digits' => 'CI number must be exactly 6 digits',
        ];
    }


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



    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function payment()
    {
        return $this->hasManyThrough(
            Payment::class, Debt::class, 'member_id', 'debt_id')->orderBy('data')->with('debt');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function previous_identities()
    {
        return $this->hasMany(PreviousIdentity::class);
    }
    public function contact_infos()
    {
        return $this->hasMany(ContactInfo::class);
    }
    public function discutii_telefonice()
    {
        return $this->hasMany(DiscutieTelefonica::class);
    }

    public function workplaces()
    {
        return $this->belongsToMany(Workplace::class, 'member_workplace')->withTimestamps();
    }

    public function member_workplace_details()
    {
        return $this->hasMany(MemberWorkplaceDetail::class);
    }

    public function note()
    {
        return $this->hasMany(Nota::class);
    }

}

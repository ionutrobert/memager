<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberWorkplaceDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'member_id',
        'workplace_id',
        'data_informatie',
        'tip_informatie',
        'data_incepere_cim',
        'data_incetare_cim',
        'tip_durata_cim',
        'tip_norma_cim',
        'functie',
        'salariu_de_baza_lunar_brut',
        'sporuri_indemnizatii_adaosuri',
        'scan_document',
        'user_id'
    ];

    public function workplace()
    {
        return $this->belongsTo(Workplace::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}

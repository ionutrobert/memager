<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscutieTelefonica extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'discutii_telefonice';

    protected $fillable = [
        'member_id',
        'contact_info_id',
        'participant_discutie',
        'rezumat',
        'data_discutie',
        'user_id',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function contact_info()
    {
        return $this->belongsTo(ContactInfo::class, 'contact_info_id');
    }



}

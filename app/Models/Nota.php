<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nota extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'note';

    protected $fillable = [
        'member_id',
        'nota',
        'user_id',
    ];
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}

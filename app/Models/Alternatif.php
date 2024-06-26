<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
    use HasFactory;
    protected $table = 'alternatif';
    protected $guarded = [];

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan_model::class, 'code_jabatan', 'code_jabatan');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan_model extends Model
{
    use HasFactory;
    protected $table = 'tb_jabatan';
    protected $guarded = [];

    public function alternatif()
    {
        return $this->hasMany(Alternatif::class, 'code_jabatan', 'code_jabatan');
    }
}

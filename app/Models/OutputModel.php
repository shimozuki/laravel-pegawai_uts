<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputModel extends Model
{
    use HasFactory;
    protected $table = 'tb_output';
    protected $guarded = [];

    public function scopeJoinJabatan($query)
    {
        return $query->join('tb_jabatan', 'tb_output.code_jabatan', '=', 'tb_jabatan.code_jabatan')->select('tb_jabatan.jabatan', 'tb_output.nama');
    }
}

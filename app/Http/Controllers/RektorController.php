<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OutputModel;
use Symfony\Component\Console\Output\Output;

class RektorController extends Controller
{
    public function index()
    {
        $pegawai = OutputModel::joinJabatan()->get();
        return view('admin.rektor.index', compact('pegawai'));
    }
}

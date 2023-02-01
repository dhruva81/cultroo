<?php

namespace App\Http\Controllers;

use App\Imports\DataImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function importPage()
    {
        return view('import');
    }

    public function importStore(Request $rqeuest)
    {
        Excel::import(new DataImport(), request()->file('file'));
        session()->flash('success', ' Records Inserted successfully!');

        return redirect()->back();
    }
}

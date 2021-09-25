<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function fileImportExport()
    {
       return view('csv-file-import');
    }
   
    // /**
    // * @return \Illuminate\Support\Collection
    // */
    public function fileImport(Request $request) 
    {
       $request->file('file')->storeAs('temp', 'task1.csv');
    }

    // /**
    // * @return \Illuminate\Support\Collection
    // */
    // public function fileExport() 
    // {
    //     return Excel::download(new UsersExport, 'users-collection.xlsx');
    // }    
}
<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\ImportCSVService;



class ImportController extends Controller
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

       $request->file('file')->storeAs('csvdata', 'task1.csv');
       $service = app(ImportCSVService::class);

        // call startCampaign
        $results = $service->import('task1.csv', 1);

        $response = [
            'success'     => true,
        ];

        return response($response, 202);

    } 

   public function fileDetailImport(Request $request) 
    {

       $request->file('file')->storeAs('csvdata', 'task2.csv');
       $service = app(ImportCSVService::class);

        // call startCampaign
        $results = $service->import('task2.csv', 2);
        
        $response = [
            'success'     => true,
        ];

        return response($response, 202);

    }

    public function updateTags(Request $request) 
    {

       $request->file('file')->storeAs('csvdata', 'task3.csv');
       $service = app(ImportCSVService::class);

        // call startCampaign
        $results = $service->import('task3.csv', 3);
        
        $response = [
            'success'     => true,
        ];

        return response($response, 202);

    }

    public function exportCsv() 
    {

       $service = app(ImportCSVService::class);

        // call startCampaign
        $results = $service->exportcsv();

        $response = [
            'success'     => true,
        ];

        return response($response, 202);

    } 
}
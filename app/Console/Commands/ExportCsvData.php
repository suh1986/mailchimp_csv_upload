<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MailChimpService;
use App\Services\CsvService;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Carbon\Carbon;




class ExportCsvData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:csv-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to export csav data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->info("\n" . "Command processing started..." . "\n");
        //build data
        $this->buildData();
        $this->info("\n\n" . "Command processing completed..." . "\n");

    }

    protected function buildData()
    {
        // build filename
        $filename = 'final-contacts-csv-data';
        $filename = $filename . "-" . \Carbon\Carbon::now()->format('YmdHs') . '.csv';
        $csvService = new CsvService($filename);

        $service = app(MailChimpService::class);
        $results = $service->export();

        if (count($results) > 0) {
            $output = new ConsoleOutput();
            $progress = new ProgressBar($output, count($results));
            $progress->start();
            foreach ($results as $result) {


                $tagsArray = array_column($result['tags'], 'name');
                $tags = implode(', ', $tagsArray);

        
                $row = [];
                $row['First Name'] = $result['merge_fields']['FNAME'];
                $row['Last Name'] = $result['merge_fields']['FNAME'];
                $row['Email Address'] = $result['email_address'];
                $row['Address'] = $result['merge_fields']['ADDRESS'];
                $row['Phone'] = $result['merge_fields']['PHONE'];
                $row['Title'] = $result['merge_fields']['TITLE'];
                $row['Tags'] = $tags;
                $row['Birthday'] = $result['merge_fields']['BIRTHDAY'];
                $row['StreetAddress'] = $result['merge_fields']['STREETADDR'];
                $row['City'] = $result['merge_fields']['CITY'];
                $row['State'] = $result['merge_fields']['STATE'];
                $row['ZipCode'] = $result['merge_fields']['ZIPCODE'];
                $row['Country'] = $result['merge_fields']['COUNTRY'];
                $row['CountryFull'] = $result['merge_fields']['COUNTRYFUL'];
                $row['Custom Field 1'] = $result['merge_fields']['CUSTOM1'];
                $row['Custom Field 2'] = $result['merge_fields']['CUSTOM2'];
                $row['id'] = $result['id'];
                $csvService->collectData($row);
                $progress->advance();
            }
            $progress->finish();
        }
        if ($csvService->hasData()) {
            $filePath = $csvService->finish();
            $this->info("ExportCsvData->buildData() - completed.file name - [{$filename}]");
        }
    }
}


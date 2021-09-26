<?php

namespace App\Services;
use App;
use App\Services\MailChimpService;
use App\Jobs\QueueUpsertContacts;
use Log;



class ImportCSVService
{
    /**
     * Send email
     *
     * @param array $params
     *
     * @return ServiceResponse
     */
    public function import($filename, $taskId)
    {


        $data = $this->readCSVData($filename);
        if (count($data) > 0) {
            // send the contacts to mailchimp
            foreach ($data as $row) {
                QueueUpsertContacts::dispatch($row, $taskId);
            }
        }
        return [
            'success' => true,
        ];
    }

    public function readCSVData($filename)
    {

        $path =  storage_path('app/csvdata/' . $filename);
        $counter = 0;
        $headers = $data = [];
        $handle = @fopen($path, "r");
        if ($handle) {
            // Read file line by line
            while (($buffer = fgets($handle)) !== false) {
                //echo $buffer;
                //Log::debug('Buffer: '.$buffer);
                $line = str_getcsv($buffer);
                if ($counter == 0) {
                    $headers = $line;
                } else {
                    $data[] = $line;
                }

                // Increase counter
                $counter++;
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);

        } else {
            echo "Could not get handle on file";
            return;
        }
        Log::info("ImportCampaigns->Handle() - Created campaigns. Writing output CSV", [
            'csv_url' => $path,
            'headers' => $headers,
            'data' => $data,
        ]);
        return $data;
    }

    public function exportcsv()
    {

        $service = app(MailChimpService::class);
        $results = $service->export();

        dd($results);

    }
}
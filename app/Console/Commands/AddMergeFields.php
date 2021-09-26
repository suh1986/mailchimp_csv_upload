<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MailChimpService;


class AddMergeFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:merge-fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to add merge fields';

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
        $this->info("command processing adding merge fields");
        $service = app(MailChimpService::class);
        // call startCampaign

        $fieldData = [
            [
                "tag" => "ADDRESS",
                "name" => "Address",
                "type" => "address"
            ],
            [
                "tag" => "PHONE",
                "name" => "Phone",
                "type" => "text"
            ],
            [
                "tag" => "TITLE",
                "name" => "Title",
                "type" => "text"
            ],
            [
                "tag" => "BIRTHDAY",
                "name" => "Birthday",
                "type" => "text"
            ],
            [
                "tag" => "STREETADDRESS",
                "name" => "StreetAddress",
                "type" => "text"
            ], 
            [
                "tag" => "CITY",
                "name" => "City",
                "type" => "text"
            ],
            [
                "tag" => "STATE",
                "name" => "state",
                "type" => "text"
            ],
            [
                "tag" => "ZIPCODE",
                "name" => "zipcode",
                "type" => "number"
            ],
            [
                "tag" => "COUNTRY",
                "name" => "country",
                "type" => "text"
            ],
                   [
                "tag" => "COUNTRYFULL",
                "name" => "country full",
                "type" => "text"
            ],
                   [
                "tag" => "CUSTOM1",
                "name" => "custom field 1",
                "type" => "text"
            ],
                   [
                "tag" => "CUSTOM2",
                "name" => "custom field 2",
                "type" => "text"
            ],
        ];
        $results = $service->addMergeField($fieldData); 
    }
}


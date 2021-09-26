
## Overview

This project contains three api and two command to mange contacts in mailchimp api.

## Prerequisites
  PHP, Composer
  MySQL,
  MailChimp Acoount

## Installation

* Clone git repository `git clone https://github.com/suh1986/mailchimp_csv_upload.git`
(go to master branch)
* Run command `composer install` to install dependancies.
* Copy `.env.example` file to `.env` file. And change env configuration as below
  set your database details in this keys (DB_HOST,DB_PORT,DB_DATABASE,DB_USERNAME,DB_PASSWORD).
  set Queue connection to database (QUEUE_CONNECTION=database)
  set your mailchimp audiance id and apikey(MAILCHIMP_API_KEY, MAILCHIMP_AUDIENCE_ID) 

* Run migration: `php artisan migrate` from `app` directory. (assuming your mysql (`v5.7`) service is running. migration can cause some common issue if you are using older version of mysql.)
* Run `php artisan key:generate` to generate key for your application.

### API DETAILS
task  1:
Run api in postman

Method: POST
URL:http://127.0.0.1:8000/api/file-import
Body :
set key : file (select type to file)
	value: select file
	Send request

Open terminal , go to mailchimp_csv_upload , run php artisan queue:work


task  2:

first add merge fields ,to add mergefields
Open terminal , go to mailchimp_csv_upload , run php artisan add:merge-fields


Run api in postman

Method: POST
URL:http://127.0.0.1:8000/api/file-import
Body :
set key : file (select type to file)
	value: select file
	Send request

Open terminal , go to mailchimp_csv_upload , run php artisan queue:work


task  2:
Run api in postman

Method: POST
URL:http://127.0.0.1:8000/api/file-detail-import
set key : file (select type to file)
	value: select file
	Send request

Open terminal , go to mailchimp_csv_upload , run php artisan queue:work

task  3:
Run api in postman

Method: POST
URL:http://127.0.0.1:8000/api/update-tags
set key : file (select type to file)
	value: select file
	Send request

Open terminal , go to mailchimp_csv_upload , run php artisan queue:work

task  4:
Open terminal , go to mailchimp_csv_upload , run php artisan export:csv-data
After command completion you can filnd csv file 'final-contacts-csv-data-{currentdatetime}.csv'
in project storage folder

## USEFUL LINKS




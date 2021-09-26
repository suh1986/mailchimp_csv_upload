<?php

namespace App\Services;
use App;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7;
use Config;
use Log;


class MailChimpService
{


    protected $apiKey;

    protected $audienceId;

    protected $baseUrl;

    protected $exportUrl;

    public function __construct()
    {

        $this->apiKey = Config::get('services.mailchimp.api_key');
        $this->audienceId = Config::get('services.mailchimp.audienceId');
        $this->baseUrl = Config::get('services.mailchimp.url');
    }

    /**
     * Send email
     *
     * @param array $params
     *
     * @return ServiceResponse
     */
    public function upsertContact($data, $taskId)
    {

        $mergeFields = $this->generateMergeFieldArray($data, $taskId);

        try {
            $client = new Client();
            $memberId = md5(strtolower($data[2]));

          
            $url =$this->baseUrl .$this->audienceId ."/members/". $memberId;
            $contactJson = [
                "email_address" => $data[2],
                "status"=>"subscribed",
                "merge_fields" => $mergeFields
            ];
            $res = $client->request('PUT', $url, [
                'json'    => $contactJson,
                'auth' => ['anystring', $this->apiKey] 
            ]);
            if ($taskId == 2) {

                $tags = explode(',', $data[6]);
                $tagArray = $this->generateTagArray($tags, 'active');
                $this->addTags($memberId, $tagArray);
            }

        } catch (ClientException $ex) {
            $error = (json_decode((string) $ex->getResponse()->getBody(), true));
            //Log::debug('client error', $error);
            return [
                'success'     => false,
                'error'       => $error,
            ];
        } catch (\Exception $ex) {
            //Log::debug('log exception', $ex);
            return [
                'success' => false,
                'error'   => $ex->getMessage(),
            ];
        }
        $response = json_decode($res->getBody(), true);        
        return [
            'success' => true,
            'data'    => $response
        ];
    }

    public function addMergeField($data)
    {

        foreach ($data as $row) {
            try {
                $client = new Client();
                $url =$this->baseUrl .$this->audienceId ."/merge-fields";
            
                $res = $client->request('POST', $url, [
                    'json'    => $row,
                    'auth' => ['anystring', $this->apiKey] 
                ]);
                $response = json_decode($res->getBody(), true);   
            } catch (ClientException $ex) {
                $error = (json_decode((string) $ex->getResponse()->getBody(), true));
                return [
                    'success'     => false,
                    'error'       => $error,
                ];
            } catch (\Exception $ex) {
                return [
                    'success' => false,
                    'error'   => $ex->getMessage(),
                ];
            }
        }
    }

    public function generateMergeFieldArray($data, $id)
    {
        $merge_fields = [];

        $merge_fields = [
            "FNAME" => $data[0],
            "LNAME" => $data[1]
        ];
        if ($id == 2) {
            //TODO: fix birthday format
            $birthday = date("m/d", strtotime($data[7]));

            $merge_fields['ADDRESS'] = $data[3];
            $merge_fields['PHONE'] = $data[4];
            $merge_fields['TITLE'] = $data[5];
            $merge_fields['BIRTHDAY'] = $birthday;
            $merge_fields['STREETADDRESS'] = $data[8];
            $merge_fields['CITY'] = $data[9];
            $merge_fields['STATE'] = $data[10];
            $merge_fields['ZIPCODE'] = $data[11];
            $merge_fields['COUNTRY'] = $data[12];
            $merge_fields['COUNTRYFUL'] = $data[13];
            $merge_fields['CUSTOM1'] = $data[14];
            $merge_fields['CUSTOM2'] = $data[15];
        }
        return $merge_fields;
    }
    public function addTags($memberId, $tagArray)
    {
  
        $payload = ["tags" => $tagArray];

        try {
            $client = new Client();
            $url =$this->baseUrl .$this->audienceId ."/members/". $memberId . '/tags';
        
            $res = $client->request('POST', $url, [
                'json'    => $payload,
                'auth' => ['anystring', $this->apiKey] 
            ]);
            $response = json_decode($res->getBody(), true);   
        } catch (ClientException $ex) {
            $error = (json_decode((string) $ex->getResponse()->getBody(), true));
            return [
                'success'     => false,
                'error'       => $error,
            ];
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'error'   => $ex->getMessage(),
            ];
        }
        $response = json_decode($res->getBody(), true);        
        return [
            'success' => true,
            'data'    => $response
        ];

    }

    public function updateTags($data)
    {

        $removeTagsArray = $this->getTags($data);
        $removetagArray = $this->generateTagArray($removeTagsArray, 'inactive');

        $tags = explode(',', $data[6]);
        $addtagArray = $this->generateTagArray($tags, 'active');

        $tagArray = array_merge($removetagArray, $addtagArray);
        $memberId = md5(strtolower($data[2]));
        $this->addTags($memberId, $tagArray);
    }
    public function generateTagArray($tags, $status)
    {
        $tagArray = [];

        foreach ($tags as $tag) {
            if ($tag !== '') {
                array_push($tagArray , json_decode(json_encode([
                    'name' => $tag , 
                    'status'=> $status
                ])));
            }
        }
        return $tagArray;        
    }
    public function getTags($data)
    {
        $removeTags = [];

        $memberId = md5(strtolower($data[2]));

        try {
            $client = new Client();
            $url =$this->baseUrl .$this->audienceId ."/members/". $memberId . '/tags';
        
            $res = $client->request('GET', $url, [
                'auth' => ['anystring', $this->apiKey] 
            ]);
            //dd($res);
            $response = json_decode($res->getBody(), true);
            if (array_key_exists("tags", $response) && count($response["tags"]) > 0){
                $removeTags = array_column($response["tags"], 'name');
            }  
        } catch (ClientException $ex) {
            $error = (json_decode((string) $ex->getResponse()->getBody(), true));
            return $removeTags;
        } catch (\Exception $ex) {
            return $removeTags;
        }
        return $removeTags;
    }

    public function export()
    {
        $responseArray = [];
        try {
            $client = new Client();
            $url =$this->baseUrl .$this->audienceId ."/members";
            $res = $client->request('GET', $url, [
                'auth' => ['anystring', $this->apiKey], 
                'query' => ['count' => 500]

            ]);
            $response = json_decode($res->getBody(), true);
            if (array_key_exists("members", $response) && count($response["members"]) > 0){
                $responseArray = $response['members'];
            }
            return $responseArray;
        } catch (ClientException $ex) {
            //dd('call');
            $error = (json_decode((string) $ex->getResponse()->getBody(), true));
            return [
                'success'     => false,
                'error'       => $error,
            ];
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'error'   => $ex->getMessage(),
            ];
        }
        
        $response = json_decode($res->getBody(), true);        
        return [
            'success' => true,
            'data'    => $response
        ];
    }
}

  
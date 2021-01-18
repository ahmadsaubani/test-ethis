<?php

namespace App\Services\ModuleServices;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class RajaOngkirService
{
    protected $client;
    protected $host;
    protected $key;
    
    public function __construct(Client $client, Request $request)
    {
        $this->client = $client;
        $this->host = env('RAJA_ONGKIR_HOST');
        $this->key = ['key' => 'e4aac32002bb2e0f24b4b51ed52a9f4b'];
    }

    public function guzzleGet($url, $param)
    {
        $req = $this->client->get($url, $param);

        $data = $req->getBody()->getContents();
        $results = json_decode($data, true);

        return $results;
    }

    public function guzzlePost($url, $param)
    {
        $req = $this->client->post($url, $param);

        $data = $req->getBody()->getContents();
        $results = json_decode($data, true);

        return $results;

    }
    
    public function getProvincesData($data)
    {
        $url = $this->host . '/province';

        $result = $this->guzzleGet($url, [
            'headers' => $this->key
        ]);

        return $result;
    }

    public function getCitiesData($data)
    {
        $url = $this->host . '/city';
        
        $result = $this->guzzleGet($url, [
            'headers' => $this->key
        ]);

        return $result;
    }

    public function checkCostData($data)
    {
        $url = $this->host . '/cost';

        $result = $this->guzzlePost($url, [
            'headers' => $this->key,
            'form_params' => [
                'origin'        => $data['origin'],
                'destination'   => $data['destination'],
                'weight'        => $data['weight'],
                'courier'       => $data['courier']
            ]
        ]);

        return $result;
    }
}

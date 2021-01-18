<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ModuleServices\RajaOngkirService;
use Exception;


class RajaOngkirController extends Controller
{
    protected $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    public function searchProvinces(Request $request)
    {
        try {
            $listFlights = $this->rajaOngkir->getProvincesData($request->all());
    
            return $this->showResult('data found', $listFlights);
        } catch (Exception $e) {
            return $this->realErrorResponse($e);
        }
    }

    public function searchCities(Request $request)
    {
        try {
            $listFlights = $this->rajaOngkir->getCitiesData($request->all());
    
            return $this->showResult('data found', $listFlights);
        } catch (Exception $e) {
            return $this->realErrorResponse($e);
        }
    }

    public function checkCost(Request $request)
    {
        try {
            $listFlights = $this->rajaOngkir->checkCostData($request->all());
    
            return $this->showResult('data found', $listFlights);
        } catch (Exception $e) {
            return $this->realErrorResponse($e);
        }
    }
} 


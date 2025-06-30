<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KomshipService;

class LocationController extends Controller
{
    protected KomshipService $komship;

    public function __construct(KomshipService $komship)
    {
        $this->komship = $komship;
    }

    public function searchAddress(Request $req)
    {
        return response()->json(
            $this->komship->searchDomesticDestination(
                $req->query('q', ''),
                (int)$req->query('limit', 5),
                (int)$req->query('offset', 0)
            )
        );
    }

    public function calculateCost(Request $req)
    {
        $origin      = (int) $req->query('origin');
        $destination = (int) $req->query('destination');
        $weight      = (int) $req->query('weight');
        if ($weight <= 0) {
            $weight = 1000;
        }
        $courier     = $req->query('courier', 'jne');
        $price       = $req->query('price', 'lowest');

        try {
            $data = $this->komship
                ->calculateDomesticCost($origin, $destination, $weight, $courier, $price);
        } catch (\Exception $e) {
            // log error dan kembalikan fallback agar tidak 500
            \Log::error('CalculateCost failed: ' . $e->getMessage());
            return response()->json([
                'meta' => [],
                'data' => [],
                'error' => 'Gagal menghitung ongkir'
            ], 200);
        }

        return response()->json($data);
    }


    public function demo()
    {
        // 1) coba search "sinduharjo"
        $result = $this->komship->searchDomesticDestination('WANGI-WANGI', 1, 0);

        // 2) tampilkan hasil
        dd($result);
    }
}

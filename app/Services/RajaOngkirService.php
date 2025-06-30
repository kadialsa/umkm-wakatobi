<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use RuntimeException;

class RajaOngkirService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        // $this->apiKey = config('services.rajaongkir.key', '');
        // if ($this->apiKey === '') {
        //     throw new RuntimeException('RAJAONGKIR_API_KEY belum diset di .env');
        // }

        $this->apiKey = config('services.komship.api_key', '');

        if ($this->apiKey === '') {
            throw new RuntimeException('KOMSHIP_API_KEY belum diset di .env');
        }

        $type = config('services.rajaongkir.account_type', 'starter');
        if ($type === 'pro') {
            $this->baseUrl = 'https://pro.rajaongkir.com/api/v2';
        } else {
            // starter atau basic (basic sekarang sama dengan starter)
            $this->baseUrl = 'https://api.rajaongkir.com/starter';
        }
    }

    protected function headers(): array
    {
        return [
            'key'          => $this->apiKey,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    protected function unwrap(Response $res, string $path = 'rajaongkir.results'): array
    {
        return $res->json($path, []);
    }

    /** 1. Daftar semua provinsi */
    public function getAllProvinces(): array
    {
        $res = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/province");
        return $this->unwrap($res);
    }

    /** 2. Ambil provinsi berdasarkan ID */
    public function getProvinceById(int $id): ?array
    {
        $res = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/province", ['id' => $id]);
        return collect($this->unwrap($res))->first();
    }

    /** 3. Pencarian provinsi berdasarkan nama */
    public function searchProvinces(string $term): array
    {
        $res = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/province", ['province' => $term]);
        return $this->unwrap($res);
    }

    /** 4. Daftar semua kota/kabupaten */
    public function getAllCities(): array
    {
        $res = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/city");
        return $this->unwrap($res);
    }

    /** 5. Daftar kota berdasarkan ID provinsi */
    public function getCitiesByProvince(int $provinceId): array
    {
        $res = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/city", ['province' => $provinceId]);
        return $this->unwrap($res);
    }

    /** 6. Ambil kota/kabupaten berdasarkan ID */
    public function getCityById(int $id): ?array
    {
        $res = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/city", ['id' => $id]);
        return collect($this->unwrap($res))->first();
    }

    /** 7. Pencarian kota/kabupaten berdasarkan nama (opsional filter provinsi) */
    public function searchCities(string $term, int $provinceId = 0): array
    {
        $params = ['city' => $term];
        if ($provinceId) {
            $params['province'] = $provinceId;
        }
        $res = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/city", $params);
        return $this->unwrap($res);
    }

    /**
     * 8. Ambil biaya pengiriman (ongkos kirim)
     *
     * @param  int    $origin      ID kota asal
     * @param  int    $destination ID kota tujuan
     * @param  int    $weight      Berat (gram)
     * @param  string $courier     'jne'|'tiki'|'pos'
     */
    public function calculateCost(int $origin, int $destination, int $weight, string $courier = 'jne'): array
    {
        $res = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/cost", [
                'origin'      => $origin,
                'destination' => $destination,
                'weight'      => $weight,
                'courier'     => $courier,
            ]);
        return $this->unwrap($res);
    }
}

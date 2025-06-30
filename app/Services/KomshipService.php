<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KomshipService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.komship.base_url', ''), '/');
        $this->apiKey  = config('services.komship.api_key', '');

        if ($this->baseUrl === '' || $this->apiKey === '') {
            throw new \RuntimeException('KOMSHIP_BASE_URL atau KOMSHIP_API_KEY belum di–set di .env');
        }
    }

    /**
     * Header standar untuk setiap request
     */
    protected function headers(): array
    {
        return [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'key'          => $this->apiKey,
        ];
    }

    /**
     * 1) Pencarian alamat sampai tingkat kelurahan/desa
     *
     * @param string $search  kata kunci (nama kelurahan/desa)
     * @param int    $limit   jumlah maximal hasil
     * @param int    $offset  offset paging
     * @return array          ({ "meta":…, "data":[…] })
     */
    public function searchDomesticDestination(string $search, int $limit = 5, int $offset = 0): array
    {
        $url = "{$this->baseUrl}/destination/domestic-destination";

        $response = Http::withHeaders($this->headers())
            ->get($url, [
                'search' => $search,
                'limit'  => $limit,
                'offset' => $offset,
            ]);

        return $response->json();
    }

    /**
     * 2) Hitung ongkos kirim domestik
     *
     * @param int    $origin      ID kota asal (dari env KOMSHIP_ORIGIN_CITY_ID)
     * @param int    $destination ID tujuan (hasil searchDomesticDestination)
     * @param int    $weight      berat dalam gram
     * @param string $courier     daftar kurir pisahkan dengan ':' (e.g. 'jne:tiki:pos')
     * @param string $price       'lowest' atau 'highest'
     * @return array              ({ "meta":…, "data":[…] })
     */
    public function calculateDomesticCost(
        int    $origin,
        int    $destination,
        int    $weight,
        string $courier,
        string $price = 'lowest'
    ): array {
        $url = "{$this->baseUrl}/calculate/domestic-cost";

        $response = Http::withHeaders($this->headers())
            ->asForm()
            ->post($url, [
                'origin'      => $origin,
                'destination' => $destination,
                'weight'      => $weight,
                'courier'     => $courier,
                'price'       => $price,
            ]);

        return $response->json();
    }
}

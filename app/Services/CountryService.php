<?php

namespace App\Services;

use App\Interfaces\CountryInterface;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class CountryService implements CountryInterface
{
    private $countriesApi = 'https://restcountries.com/v2/all?fields=name,capital,region,population,flag,currencies';
    private $exchangeApi = 'https://open.er-api.com/v6/latest/USD';

    public function refreshCountries()
    {
        DB::beginTransaction();

        try {
            $countriesResponse = Http::get($this->countriesApi);
            $exchangeResponse = Http::get($this->exchangeApi);

            if ($countriesResponse->failed()) {
                return response()->json([
                    'error' => 'External data source unavailable',
                    'details' => 'Could not fetch data from RestCountries API'
                ], 503);
            }

            if ($exchangeResponse->failed()) {
                return response()->json([
                    'error' => 'External data source unavailable',
                    'details' => 'Could not fetch data from Exchange Rate API'
                ], 503);
            }

            $countriesData = $countriesResponse->json();
            $exchangeData = $exchangeResponse->json()['rates'] ?? [];

            $now = now();
            foreach ($countriesData as $data) {
                $currency = $data['currencies'][0]['code'] ?? null;
                $exchange = $currency && isset($exchangeData[$currency]) ? $exchangeData[$currency] : null;
                $randomMultiplier = rand(1000, 2000);
                $gdp = ($exchange && $exchange > 0)
                    ? ($data['population'] * $randomMultiplier) / $exchange
                    : 0;

                Country::updateOrCreate(
                    ['name' => $data['name']],
                    [
                        'capital' => $data['capital'] ?? null,
                        'region' => $data['region'] ?? null,
                        'population' => $data['population'] ?? 0,
                        'currency_code' => $currency,
                        'exchange_rate' => $exchange,
                        'estimated_gdp' => $gdp,
                        'flag_url' => $data['flag'] ?? null,
                        'last_refreshed_at' => $now,
                    ]
                );
            }

            DB::commit();

           $this->generateSummaryImage($now);

            return response()->json(['message' => 'Countries refreshed successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Country refresh failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

private function generateSummaryImage($timestamp)
{
    $total = Country::count();
    $topCountries = Country::orderByDesc('estimated_gdp')->take(5)->get(['name', 'estimated_gdp']);

    $content = "🌍 Country Summary\n\nTotal Countries: {$total}\n\nTop 5 GDP:\n";
    foreach ($topCountries as $c) {
        $content .= "{$c->name}: " . number_format($c->estimated_gdp, 2) . "\n";
    }
    $content .= "\nLast Refresh: {$timestamp}";

    // Create blank image
    $width = 600;
    $height = 400;
    $img = imagecreatetruecolor($width, $height);

    // Colors
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);

    // Fill background
    imagefilledrectangle($img, 0, 0, $width, $height, $white);

    // Set font
    $fontPath = public_path('fonts/DejaVuSans.ttf');
    $fontSize = 12;
    $y = 30;
    foreach (explode("\n", $content) as $line) {
        imagettftext($img, $fontSize, 0, 30, $y, $black, $fontPath, $line);
        $y += 20;
    }

    $path = public_path('cache/summary.png');
    if (!file_exists(public_path('cache'))) {
        mkdir(public_path('cache'), 0777, true);
    }
    imagepng($img, $path);
    imagedestroy($img);
}


}

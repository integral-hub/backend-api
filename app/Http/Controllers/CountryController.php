<?php

namespace App\Http\Controllers;

use App\Interfaces\CountryInterface;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function __construct(
        private readonly CountryInterface $countryService
    ){}

    /**
     * Refresh the list of countries.
     *
     * Refreshes country data from an external source and updates the database.
     *
     * @group Countries
     * @response 200 {
     *   "message": "Countries refreshed successfully",
     *   "total": 195
     * }
     */
    // POST /countries/refresh
    public function refresh()
    {
        return $this->countryService->refreshCountries();
    }

    /**
     * Get a list of countries.
     *
     * Returns all countries, with optional filters for region and currency, and sorting by GDP.
     *
     * @group Countries
     * @queryParam region string Optional. Filter by region name. Example: Europe
     * @queryParam currency string Optional. Filter by currency code. Example: USD
     * @queryParam sort string Optional. Sort by GDP ascending or descending. Allowed values: gdp_asc, gdp_desc. Example: gdp_desc
     * @response 200 [{
     *   "id": 1,
     *   "name": "France",
     *   "region": "Europe",
     *   "currency_code": "EUR",
     *   "estimated_gdp": 2800000000000
     * }]
     */
    // GET /countries
    public function index(Request $request)
    {
        $query = Country::query();

        if ($request->region) $query->where('region', $request->region);
        if ($request->currency) $query->where('currency_code', $request->currency);

        if ($request->sort === 'gdp_desc') $query->orderByDesc('estimated_gdp');
        elseif ($request->sort === 'gdp_asc') $query->orderBy('estimated_gdp');

        return response()->json($query->get());
    }

    /**
     * Get details of a specific country.
     *
     * Returns detailed information for a given country by name.
     *
     * @group Countries
     * @urlParam name string required The name of the country. Example: France
     * @response 200 {
     *   "id": 1,
     *   "name": "France",
     *   "region": "Europe",
     *   "currency_code": "EUR",
     *   "estimated_gdp": 2800000000000
     * }
     * @response 404 {
     *   "error": "Country not found"
     * }
     */
    // GET /countries/{name}
    public function show($name)
    {
        $country = Country::where('name', 'like', $name)->first();
        if (!$country) return response()->json(['error' => 'Country not found'], 404);
        return response()->json($country);
    }

    /**
     * Delete a country.
     *
     * Deletes a country by name.
     *
     * @group Countries
     * @urlParam name string required The name of the country. Example: France
     * @response 200 {
     *   "message": "Country deleted successfully"
     * }
     * @response 404 {
     *   "error": "Country not found"
     * }
     */
    // DELETE /countries/{name}
    public function destroy($name)
    {
        $country = Country::where('name', 'like', $name)->first();
        if (!$country) return response()->json(['error' => 'Country not found'], 404);
        $country->delete();
        return response()->json(['message' => 'Country deleted successfully']);
    }

    /**
     * Get system status and last refresh info.
     *
     * Returns total number of countries and the timestamp of the last refresh.
     *
     * @group Status
     * @response 200 {
     *   "total_countries": 195,
     *   "last_refreshed_at": "2025-10-27T18:32:14.000000Z"
     * }
     */
    // GET /status
    public function status()
    {
        $latest = Country::max('last_refreshed_at');
        return response()->json([
            'total_countries' => Country::count(),
            'last_refreshed_at' => $latest,
        ]);
    }

    /**
     * Get the summary image of countries.
     *
     * Returns a cached PNG image summarizing country data.
     *
     * @group Countries
     * @response file binary The summary image.
     * @response 404 {
     *   "error": "Summary image not found"
     * }
     */
    // GET /countries/image
public function imageView()
{
    $path = public_path('cache/summary.png');
    if (!file_exists($path)) {
        return response()->json(['error' => 'Summary image not found']);
    }
    return response()->file($path);
}

}

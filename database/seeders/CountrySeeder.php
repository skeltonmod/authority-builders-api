<?php

namespace Database\Seeders;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Rinvex\Country\CountryLoader;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new Client(['base_uri' => 'https://restcountries.com/v3.1/']);
        $response = $client->get('all');
        $countries = json_decode($response->getBody()->getContents(), true);

        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'name' => $country['name']['common'],
                'shortcode' => $country['cca2'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    /**
     * Show the form to search for weather.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function searchWeather()
    {
        return view('weather');
    }

    /**
     * Show the form to search for weather forecast.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function searchForecast()
    {
        return view('weather.forecast');
    }

    /**
     * Get the current weather data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function getWeather(Request $request)
    {
        try {
            $request->validate([
                'city' => 'required|string',
            ]);

            $city = $request->input('city');
            $apiKey = config('services.weather_api.key');
            $response = Http::get("http://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey");

            if ($response->successful()) {
                $weatherData = $response->json();
                return view('weather', compact('weatherData'));
            } else {
                $errorMessage = $response['message'];
                return view('weather', compact('errorMessage'));
            }
        } catch (\Exception $e) {
            Log::error('Error fetching weather data: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while fetching weather data.']);
        }
    }

    /**
     * Get the weather forecast data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function getForecast(Request $request)
    {
        try {
            $request->validate([
                'city' => 'required|string',
            ]);

            $city = $request->input('city');
            $apiKey = config('services.weather_api.key');

            // Construct the Geocoding API URL to get latitude and longitude
            $geoUrl = "https://api.openweathermap.org/geo/1.0/direct?q={$city}&limit=1&appid={$apiKey}";

            // Fetch latitude and longitude
            $geoResponse = Http::get($geoUrl);
            $geoData = $geoResponse->json();

            // Check if the city was found
            if (empty($geoData)) {
                return back()->withErrors(['city' => 'City not found. Please enter a valid city name.']);
            }

            $latitude = $geoData[0]['lat'];
            $longitude = $geoData[0]['lon'];

            // Construct the Weather Forecast API URL
            $forecastUrl = "https://api.openweathermap.org/data/2.5/forecast?lat={$latitude}&lon={$longitude}&appid={$apiKey}";

            // Fetch weather forecast data
            $forecastResponse = Http::get($forecastUrl);
            $forecastData = $forecastResponse->json();

            // Extract relevant forecast information
            $forecastList = [];
            foreach ($forecastData['list'] as $forecast) {
                $forecastList[] = [
                    'date' => $forecast['dt_txt'],
                    'temp' => $forecast['main']['temp'],
                    'weather' => $forecast['weather'][0]['description']
                ];
            }

            return view('weather.forecast', compact('forecastList'));
        } catch (\Exception $e) {
            Log::error('Error fetching weather forecast data: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while fetching weather forecast data.']);
        }
    }
}

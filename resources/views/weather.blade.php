<!-- resources/views/weather.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Card</title>
    <link href="{{ asset('css/weather.css') }}" rel="stylesheet">
</head>
<body>
    <div class="form">
        <form method="post" action="{{ url('/weather') }}">
            @csrf
            <input type="text" class="text" placeholder="Enter city name" name="city" value="{{ old('city') }}" required/>
            <input type="submit" value="Get Weather" class="submit" name="submit"/>
            @isset($errorMessage)
                <p class="error">{{ $errorMessage }}</p>
            @endisset
        </form>
    </div>

    @isset($weatherData)
        <article class="widget">
            <div class="weatherIcon">
                <img src="http://openweathermap.org/img/wn/{{ $weatherData['weather'][0]['icon'] }}@4x.png" alt="Weather Icon"/>
            </div>
            <div class="weatherInfo">
                <div class="temperature">{{ round($weatherData['main']['temp']-273.15) }}°C</div>
                <div class="description mr45">
                    <div class="weatherCondition">{{ $weatherData['weather'][0]['main'] }}</div>
                    <div class="place">{{ $weatherData['name'] }}</div>
                </div>
                <div class="description">
                    <div class="weatherCondition">Wind</div>
                    <div class="place">{{ $weatherData['wind']['speed'] }} M/H</div>
                </div>
            </div>
            <div class="date">{{ date('d M', $weatherData['dt']) }}</div>
        </article>
    @endisset
</body>
</html>

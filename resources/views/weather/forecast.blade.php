<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Forecast</title>
    <link href="{{ asset('css/forecast.css') }}" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="form">
        <h1>Weather Forecast</h1>
        <form method="post" action="{{ '/forecast' }}">
            @csrf
            <input type="text" class="text" placeholder="Enter city name" name="city" value="{{ old('city') }}"/>
            <button type="submit" class="submit">Get Forecast</button>
            @error('city')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </form>
    </div>

    @isset($forecastList)
        <table>
            <tr>
                <th>Date</th>
                <th>Average Temperature (K)</th>
                <th>Weather</th>
            </tr>
            @php
                $forecastByDay = [];
                foreach ($forecastList as $forecast) {
                    $date = date('Y-m-d', strtotime($forecast['date']));
                    if (!isset($forecastByDay[$date])) {
                        $forecastByDay[$date] = [
                            'date' => $date,
                            'totalTemp' => $forecast['temp'],
                            'weather' => $forecast['weather']
                        ];
                    } else {
                        $forecastByDay[$date]['totalTemp'] += $forecast['temp'];
                    }
                }
            @endphp
            @foreach($forecastByDay as $dayForecast)
                <tr>
                    <td>{{ $dayForecast['date'] }}</td>
                    <td>{{ round($dayForecast['totalTemp'] / 8, 2) }}</td>
                    <td>{{ $dayForecast['weather'] }}</td>
                </tr>
            @endforeach
        </table>
    @endisset
</div>

</body>
</html>

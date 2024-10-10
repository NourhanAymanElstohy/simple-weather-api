<?php

namespace App\Interfaces;

interface WeatherServiceInterface
{
    public function getWeather(string $city): array;
}

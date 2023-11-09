<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Http;

Trait ApiTrait
{
    public static function getSalesEvent()
    {
        $responseEH = [];$responseED = [];$responseINT = [];$responseEvent = [];

        if(app()->environment() == 'production'){
            $responseEH = Http::get('https://bosemzi.com/api/getSalesEvent');
            $responseED = Http::get('https://aa.bosemzi.com/api/getSalesEvent');
            $responseINT = Http::get('https://int.bosemzi.com/api/getSalesEvent');
        }else{
            $responseEH = Http::get('https://ecomstg.groobok.com/api/getSalesEvent');
            $responseED = Http::get('https://qastg.groobok.com//api/getSalesEvent');
            $responseINT = Http::get('https://stg.groobok.com/api/getSalesEvent');
        }

        $responseEH = $responseEH->successful() ? $responseEH->json() : [];

        $responseED = $responseED->successful() ? $responseED->json() : [];

        $responseINT = $responseINT->successful() ? $responseINT->json() : [];

        $responseEvent = array_filter([$responseEH, $responseED, $responseINT]);

        $responseEvent = array_merge(...$responseEvent);

        return collect($responseEvent);

    }
}
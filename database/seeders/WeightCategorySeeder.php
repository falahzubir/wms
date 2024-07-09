<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WeightCategory;

class WeightCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['weight' => 0.5, 'category' => 'G0'],
            ['weight' => 1, 'category' => 'G1'],
            ['weight' => 1.5, 'category' => 'G2'],
            ['weight' => 2, 'category' => 'G3'],
            ['weight' => 2.5, 'category' => 'G4'],
            ['weight' => 3, 'category' => 'G5'],
            ['weight' => 3.5, 'category' => 'G6'],
            ['weight' => 4, 'category' => 'G7'],
            ['weight' => 4.5, 'category' => 'G8'],
            ['weight' => 5, 'category' => 'G9'],
            ['weight' => 5.5, 'category' => 'G10'],
            ['weight' => 6, 'category' => 'G11'],
            ['weight' => 6.5, 'category' => 'G12'],
            ['weight' => 7, 'category' => 'G13'],
            ['weight' => 7.5, 'category' => 'G14'],
            ['weight' => 8, 'category' => 'G15'],
            ['weight' => 8.5, 'category' => 'G16'],
            ['weight' => 9, 'category' => 'G17'],
            ['weight' => 9.5, 'category' => 'G18'],
            ['weight' => 10, 'category' => 'G19'],
            ['weight' => 10.5, 'category' => 'G20'],
            ['weight' => 11, 'category' => 'G21'],
            ['weight' => 11.5, 'category' => 'G22'],
            ['weight' => 12, 'category' => 'G23'],
            ['weight' => 12.5, 'category' => 'G24'],
            ['weight' => 13, 'category' => 'G25'],
            ['weight' => 13.5, 'category' => 'G26'],
            ['weight' => 14, 'category' => 'G27'],
            ['weight' => 14.5, 'category' => 'G28'],
            ['weight' => 15, 'category' => 'G29'],
            ['weight' => 15.5, 'category' => 'G30'],
            ['weight' => 16, 'category' => 'G31'],
            ['weight' => 16.5, 'category' => 'G32'],
            ['weight' => 17, 'category' => 'G33'],
            ['weight' => 17.5, 'category' => 'G34'],
            ['weight' => 18, 'category' => 'G35'],
            ['weight' => 18.5, 'category' => 'G36'],
            ['weight' => 19, 'category' => 'G37'],
            ['weight' => 19.5, 'category' => 'G38'],
            ['weight' => 20, 'category' => 'G39'],
            ['weight' => 20.5, 'category' => 'G40'],
            ['weight' => 21, 'category' => 'G41'],
            ['weight' => 21.5, 'category' => 'G42'],
            ['weight' => 22, 'category' => 'G43'],
            ['weight' => 22.5, 'category' => 'G44'],
            ['weight' => 23, 'category' => 'G45'],
            ['weight' => 23.5, 'category' => 'G46'],
            ['weight' => 24, 'category' => 'G47'],
            ['weight' => 24.5, 'category' => 'G48'],
            ['weight' => 25, 'category' => 'G49'],
            ['weight' => 25.5, 'category' => 'G50'],
            ['weight' => 26, 'category' => 'G51'],
            ['weight' => 26.5, 'category' => 'G52'],
            ['weight' => 27, 'category' => 'G53'],
            ['weight' => 27.5, 'category' => 'G54'],
            ['weight' => 28, 'category' => 'G55'],
            ['weight' => 28.5, 'category' => 'G56'],
            ['weight' => 29, 'category' => 'G57'],
            ['weight' => 29.5, 'category' => 'G58'],
            ['weight' => 30, 'category' => 'G59'],
            ['weight' => 30.5, 'category' => 'G60'],
            ['weight' => 31, 'category' => 'G61'],
            ['weight' => 31.5, 'category' => 'G62'],
            ['weight' => 32, 'category' => 'G63'],
            ['weight' => 32.5, 'category' => 'G64'],
            ['weight' => 33, 'category' => 'G65'],
            ['weight' => 33.5, 'category' => 'G66'],
            ['weight' => 34, 'category' => 'G67'],
            ['weight' => 34.5, 'category' => 'G68'],
            ['weight' => 35, 'category' => 'G69'],
            ['weight' => 35.5, 'category' => 'G70'],
            ['weight' => 36, 'category' => 'G71'],
            ['weight' => 36.5, 'category' => 'G72'],
            ['weight' => 37, 'category' => 'G73'],
            ['weight' => 37.5, 'category' => 'G74'],
            ['weight' => 38, 'category' => 'G75'],
        ];

        foreach ($data as $item) {
            $rs['name'] = $item['category'];
            $rs['min_weight'] = $item['weight'] * 1000;
            $rs['max_weight'] = ($item['weight'] + 0.5) * 1000;
            unset($rs['weight']);
            WeightCategory::firstOrCreate($rs);
        }
    }
}

<?php

use Illuminate\Database\Seeder;

class OpeningHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(\App\Models\Business::all() as $business) {
            $business->opening_hours_info = json_encode($this->generateOpeningHours());
            $business->save();
        }
    }

    private function generateOpeningHours()
    {
        return [
            'monday' => [
                ['hours' => '08:00-22:00'],
            ],
            'tuesday' => [
                ['hours' => '08:00-22:00'],
            ],
            'wednesday' => [
                ['hours' => '08:00-22:00'],
            ],
            'thursday' => [
                ['hours' => '08:00-22:00'],
            ],
            'friday' => [
                ['hours' => '08:00-22:00'],
            ],
            'saturday' => [
                ['hours' => '10:00-18:00'],
            ],
        ];
    }

}

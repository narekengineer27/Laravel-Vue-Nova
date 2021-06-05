<?php

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();

        $position = 1;

        $bar = $this->command->getOutput()->createProgressBar(2000);

        $bar->start();

        for ($i = 1; $i <= 2000; $i++) {
            $businesses = [];
            $categories = [];
            $review = [];
            $post = [];
            $business_category = [];

            for ($j = 0; $j < 100; $j++) {
                $businesses[] = [
                    'id' => $position,
                    'name' => 'business_name'.$position,
                    'lat' => $faker->latitude(50.911122, 57.981733),
                    'lng' => $faker->longitude(-6.132738, 1.183590),
                ];

                $business_category[] = [
                    'business_id' => $position,
                    'category_id' => $i,
                    'relevance' => 1,
                ];

                $position++;
            }

            $review[] = [
                'business_id' => $i,
                'user_id' => 1,
                'score' => 1,
            ];
            
            $post[] = [
                'business_id' => $i,
                'user_id' => 1,
            ];

            $categories[] = [
                'id' => $i,
                'uuid' => 'category_uuid'.$i,
                'name' => 'category'.$i,
            ];

            DB::table('businesses')->insert($businesses);
            DB::table('categories')->insert($categories);
            DB::table('business_reviews')->insert($review);
            DB::table('business_posts')->insert($post);
            DB::table('business_category')->insert($business_category);

            $bar->advance();
        }

        $bar->finish();
    }
}

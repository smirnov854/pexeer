<?php

namespace Database\seeds;

use Illuminate\Database\Seeder;

class CustomLandingTradeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('custom_landing_trade')->delete();

        \DB::table('custom_landing_trade')->insert(array (
            0 =>
                array (
                    'id' => 22,
                    'landing_page_id' => 2,
                    'image_one' => 'landing/trade/oGUI9O10aoqUZycNr52lgLRaHdhZs4F6iDoUhdqU.png',
                    'image_two' => 'landing/trade/Ol4Sk5uKZhmo9wpKObeV02uxIpvCFcskJ1Gz5eQo.png',
                    'video_link' => NULL,
                    'app_store_link' => 'https://pexeer-demo.itech-theme.com/',
                    'play_store_link' => 'https://pexeer-demo.itech-theme.com/',
                    'android_apk_link' => 'https://pexeer-demo.itech-theme.com/',
                    'windows_link' => 'https://pexeer-demo.itech-theme.com/',
                    'linux_link' => 'https://pexeer-demo.itech-theme.com/',
                    'mac_link' => 'https://pexeer-demo.itech-theme.com/',
                    'api_link' => 'https://pexeer-demo.itech-theme.com/',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
        ));


    }
}

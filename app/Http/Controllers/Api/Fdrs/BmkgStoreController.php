<?php

namespace App\Http\Controllers\Api\Fdrs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Goutte\Client;
use Illuminate\Support\Facades\Storage;
use App\Models\Fdrs\DataFdrs;

class BmkgStoreController extends Controller
{
    public function index(Request $request)
    {        
        set_time_limit(600);

        //Options
        $option = new BmkgController();
        $optionWilayah  = $option->optionWilayah();
        $index = 'dc';
        $hari  = 'obs';

        // foreach ($optionWilayah as $key => $wilayah) {
        //     Storage::disk('publicNas')->makeDirectory('fdrs/'.$key);
        //     //sleep();
        // }

        foreach ($optionWilayah as $key => $wilayah) {
            $_check_data = DataFdrs::where(
                [
                    'fdrs_option_wilayah_key' => $key,
                    'fdrs_option_index_key'   => $index,
                    'fdrs_option_hari_key'    => $hari,
                    'date' => Date('Y-m-d')
                ]
            )->first();

            if(!$_check_data){
                // Get Data
                $data  = $this->store($index, $key,$wilayah,$hari);
                // Store
                DataFdrs::firstOrCreate(
                    [
                        'fdrs_option_wilayah_key' => $key,
                        'fdrs_option_index_key'   => $index,
                        'fdrs_option_hari_key'    => $hari,
                    ],
                    $data
                );
            }
        }

        return ['status' => true, 'messaga' => 'success'];
    }

    public function store($index,$wilayah_key,$wilayah,$hari){
        //Scraping
        $link = 'https://www.bmkg.go.id/cuaca/kebakaran-hutan.bmkg?index='.$index.'&wil='.$wilayah_key.'&day='.$hari;
        $client = new Client();
        $crawler = $client->request('GET', $link);

        //Get Image
        $path = $crawler->filter('.featurette-image')->eq(0)->attr('src');
        
        //Save Image
        $date       = Date('dMY');
        $filename   = str_replace(' ','_',$wilayah).'_'.$hari.'_'.$index.'_'.$date.'.png';
        $image = \Image::make($path);

        //Crop Image
        if($wilayah_key == 'indonesia')
        {
            $image->crop(2293, 1162,25,330);
        }else if($wilayah_key == 'asean')
        {
            $image->crop(1821, 1345,25, 295);
        }else{
            $image->crop(1467, 1601, 20, 30);
        }

        //Save image
        $saveImage = Storage::disk('publicNas')->put("fdrs/".$wilayah_key."/".$filename, $image->stream(),'public');
        
        // Set Data
        if($saveImage)
        {
            $_data = [
                'fdrs_option_wilayah_key' => $wilayah_key,
                'fdrs_option_index_key'  => $index,
                'fdrs_option_hari_key'   => $hari,
                'date'    => Date('Y-m-d'),
                'image'   => $filename,
            ];
        }

        return $_data;
    }
}

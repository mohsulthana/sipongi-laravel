<?php

namespace App\Http\Controllers\Api\Fdrs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Goutte\Client;

class BmkgController extends Controller
{
    public function index(Request $request)
    {   
        //Params
        $index   = $request->index;
        $wilayah = $request->wilayah;
        $hari    = $request->hari;

        //Scraping
        $link = 'https://www.bmkg.go.id/cuaca/kebakaran-hutan.bmkg?index='.$index.'&wil='.$wilayah.'&day='.$hari;
        $client = new Client();
        $crawler = $client->request('GET', $link);

        //Options
        $optionIndex    = $this->optionIndex();
        $optionWilayah  = $this->optionWilayah();
        $optionHari     = $this->optionHari();

        //Get Image
        return [
            'data' => [
                'index'   => $optionIndex[$index],
                'wilayah' => $optionWilayah[$wilayah],
                'hari'    => $optionHari[$hari],
                'image'   => $crawler->filter('.featurette-image')->eq(0)->attr('src') 
            ],
            'option' => $this->options()
        ];
    }

    public function options(){
        //Options
        $optionIndex    = $this->optionIndex();
        $optionWilayah  = $this->optionWilayah();
        $optionHari     = $this->optionHari();

        $_index = [];
        foreach ($optionIndex as $key => $index) {
            array_push($_index, [
                'value' => $key,
                'text'  => $index,
            ]);
        }

        $_wilayah = [];
        foreach ($optionWilayah as $key1 => $wilayah) {
            array_push($_wilayah, [
                'value' => $key1,
                'text'  => $wilayah,
            ]);
        }

        $_hari = [];
        foreach ($optionHari as $key2 => $hari) {
            array_push($_hari, [
                'value' => $key2,
                'text'  => $hari,
            ]);
        }

        $response = [
            'index'   => $_index,
            'wilayah' => $_wilayah,
            'hari'    => $_hari,
        ];

        return $response;
    }

    public function optionWilayah()
    {
        $data = [
            "aceh"        => "Aceh",
            "sumut"       => "Sumatera Utara",
            "sumbar"      => "Sumatera Barat",
            "riau"        => "Riau",
            "kepri"       => "Kepulauan Riau",
            "jambi"       => "Jambi",
            "bengkulu"    => "Bengkulu",
            "sumsel"      => "Sumatera Selatan",
            "babel"       => "Bangka Belitung",
            "lampung"     => "Lampung",
            "banten"      => "Banten",
            "dkijakarta"  => "DKI Jakarta",
            "jabar"       => "Jawa Barat",
            "jateng"      => "Jawa Tengah",
            "jatim"       => "Jawa Timur",
            "yogya"       => "DI Yogyakarta",
            "bali"        => "Bali",
            "ntb"         => "Nusa Tenggara Barat",
            "ntt"         => "Nusa Tenggara Timur",
            "kalsel"      => "Kalimantan Selatan",
            "kalbar"      => "Kalimantan Barat",
            "kalteng"     => "Kalimantan Tengah",
            "kaltim"      => "Kalimantan Timur",
            "kaltara"     => "Kalimantan Utara",
            "gorontalo"   => "Gorontalo",
            "sulbar"      => "Sulawesi Barat",
            "sulsel"      => "Sulawesi Selatan",
            "sultra"      => "Sulawesi Tenggara",
            "sulteng"     => "Sulawesi Tengah",
            "sulut"       => "Sulawesi Utara",
            "maluku"      => "Maluku",
            "malut"       => "Maluku Utara",
            "pabar"       => "Papua Barat",
            "papua"       => "Papua",
            "asean"       => "ASEAN",
            "indonesia"   => "Indonesia",
            "sumatera"    => "Sumatera",
            "kalimantan"  => "Kalimantan",
        ];

        return $data;
    }

    public function optionHari()
    {
        $data = [
            "obs" => "observasi",
			"0"   => "Hari ini",
			"1"   => "Esok hari",
			"2"   => "Esok lusa",
			"3"   => "+3 hari",
			"4"   => "+4 hari",
			"5"   => "+5 hari",
			"6"   => "+6 hari",
			"7"   => "+7 hari",
        ];

        return $data;
    }

    public function optionIndex()
    {
        $data = [
            "ffmc"=> "Fine Fuel Moisture Code",
			"dmc" => "Duff Moisture Code",
			"dc"  => "Drought Code",
			"bui" => "Build Up Index",
			"isi" => "Initial Spread Index",
			"fwi" => "Fire Weather Index",
        ];

        return $data;
    }
}

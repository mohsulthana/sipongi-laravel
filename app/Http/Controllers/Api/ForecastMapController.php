<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ForecastMap\AqmsResources;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class ForecastMapController extends Controller
{
    public function getGfs()
    {
        try {
            $http = new Client;
            $res = $http->get("http://suitability-mapper.s3.amazonaws.com/wind/wind-surface-level-gfs-1.0.gz.json");
            if ($res->getStatusCode() === 200) {
                return response()->json(json_decode($res->getBody()));
            } else {
                return response()->json(array(
                    'status' => false,
                    'message' => __('exception.handler.500'),
                ), $res->getStatusCode());
            }
        } catch (ClientException $e) {
            return response()->json(array(
                'status' => false,
                'message' => __('exception.handler.500'),
                'errors' => $e
            ), 500);
        }
    }

    public function getAqms()
    {
        try {
            $http = new Client;
            $res = $http->get("http://iku.menlhk.go.id/aqms");
            if ($res->getStatusCode() === 200) {
                $content = preg_replace("/\n|\r/i", "", $res->getBody()->getContents());
                $result = preg_replace("/^.*?var\s+objMap\s+=\s+(.*?\]);.*$/i", "$1", $content);

                return new AqmsResources(json_decode($result));
            } else {
                return response()->json(array(
                    'status' => false,
                    'message' => __('exception.handler.500'),
                ), $res->getStatusCode());
            }
        } catch (ClientException $e) {
            return response()->json(array(
                'status' => false,
                'message' => __('exception.handler.500'),
                'errors' => $e
            ), 500);
        }
    }
}

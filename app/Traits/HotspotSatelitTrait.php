<?php

namespace App\Traits;

trait HotspotSatelitTrait
{
    public function hotspot_name($hotspot, $actual = '')
    {
        switch ($hotspot) {
            case "NOAA18":
                return ($actual) ? "NOAA18 (ASMC)" : "NOAA (ASMC)";
            case "NOAA19":
                return ($actual) ? "NOAA19 (ASMC)" : "NOAA (ASMC)";
            case "TERRA":
                return "TERRA (ASMC)";
            case "AQUA":
                return "AQUA (ASMC)";
            case "NASA-MODIS":
                return "TERRA / AQUA (NASA)";
            case "LAPAN-TERA":
                return "TERRA (LAPAN)";
            case "LAPAN-AQUA":
                return "AQUA (LAPAN)";
            case "LAPAN-NPP":
                return "NPP (LAPAN)";
            case "LPN-MODIS":
                return "TERRA/AQUA (LAPAN)";
            case "LPN-TERRA":
                return ($actual) ? "TERRA (LAPAN)" : "TERRA/AQUA (LAPAN)";
            case "LPN-AQUA":
                return ($actual) ? "AQUA (LAPAN)" : "TERRA/AQUA (LAPAN)";
            case "LPN-NOAA20":
                return ($actual) ? "NOAA20 (LAPAN)" : "NOAA (LAPAN)";
            default:
                return $hotspot;
        }
    }

    public function number_format_short($n)
    {
        if ($n <= 0) {
            $n_format = floor($n);
            $suffix = '';
        } else if ($n > 0 && $n < 1000) {
            // 1 - 999
            $n_format = floor($n);
            $suffix = '';
        } else if ($n >= 1000 && $n < 1000000) {
            // 1k-999k
            $n_format = floor($n / 1000);
            $suffix = 'K+';
        } else if ($n >= 1000000 && $n < 1000000000) {
            // 1m-999m
            $n_format = floor($n / 1000000);
            $suffix = 'M+';
        } else if ($n >= 1000000000 && $n < 1000000000000) {
            // 1b-999b
            $n_format = floor($n / 1000000000);
            $suffix = 'B+';
        } else if ($n >= 1000000000000) {
            // 1t+
            $n_format = floor($n / 1000000000000);
            $suffix = 'T+';
        }

        return !empty($n_format . $suffix) ? $n_format . $suffix : 0;
    }
}

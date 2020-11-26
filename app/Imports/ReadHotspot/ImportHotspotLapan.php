<?php

namespace App\Imports\ReadHotspot;

use App\Models\HotspotSatelit;
use App\Traits\ImportHotspot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportHotspotLapan implements ToCollection, WithChunkReading, WithStartRow
{
    use Importable, ImportHotspot;

    private $setting;

    public function __construct($setting)
    {
        $this->setting = $setting;
    }

    public function collection(Collection $rows)
    {
        activity()->disableLogging();
        $insertData = array();
        foreach ($rows as $col) {
            if (!$col->filter()->isEmpty() && !empty($col[11])) { // check column row not empty and total column harus lebih dari 11
                $x = (float) $col[4]; //longitude
                $y = (float) $col[3]; //latitude
                $date = $col[1]; // date format wajib Y-m-d
                $time = $col[2]; // time format 00:00:00/H:i:s
                $parseDate = Carbon::parse("$date $time");
                $date_hotspot = $parseDate->format('Y-m-d H:i:s');
                $confidence = (int) $col[5];
                $brightness = 0;
                $tipe = $col[11];
                $baseSumber = 'LPN-?';
                $sumber = Str::replaceArray('?', [Str::upper(trim($col[6]))], $baseSumber);
                $source = 'lapan';
                $level = 'low';

                if ($confidence === 7) {
                    $level = 'low';
                } else if ($confidence === 8) {
                    $level = 'medium';
                } else if ($confidence === 9) {
                    $level = 'high';
                }

                $sumber = str_replace("SNPP", "NPP", $sumber);
                $oriSumber = $sumber;
                $sumber = str_replace("TERRA", "MODIS", $sumber);
                $sumber = str_replace("AQUA", "MODIS", $sumber);

                $count = HotspotSatelit::query()
                    ->whereDate('date_hotspot', $parseDate->format('Y-m-d'))
                    ->where('x', $x)
                    ->where('y', $y)
                    ->where('sumber', $sumber)
                    ->count();
                if ($count <= 0) {
                    try {
                        DB::select("select ST_Transform(ST_SetSRID(ST_Point($x,$y), 4326),3857) as distance");
                    } catch (\Exception $e) {
                        $temp = $x;
                        $x = $y;
                        $y = $temp;
                        $temp = null;
                    }

                    $counterData = $this->counterData($x, $y, $parseDate, $this->setting, $sumber);
                    $counter = $counterData ? (int) $counterData->counter + 1 : 1;

                    $fungsiKawasan = $this->fungsiKawasan($x, $y);

                    $deletedAt = null;
                    $petaKawasan = $this->petaKawasan($x, $y);
                    if ($petaKawasan) {
                        $deletedAt = Str::lower($petaKawasan->fungsi) === 'tubuh air' ? Carbon::now() : null;
                    }

                    $kelId = null;
                    $namaKel = null;
                    $kecId = null;
                    $namaKec = null;
                    $kabId = null;
                    $namaKab = null;
                    $provId = null;
                    $namaProv = 'LUAR INDONESIA';

                    $checkKel1 = $this->checkKel($x, $y);
                    if ($checkKel1) {
                        $kelId = $checkKel1->id;
                        $namaKel = $checkKel1->nama;
                        $kecId = $checkKel1->kecamatan->id;
                        $namaKec = $checkKel1->kecamatan->nama;
                        $kabId = $checkKel1->kecamatan->kota_kab->id;
                        $namaKab = $checkKel1->kecamatan->kota_kab->nama;
                        $provId = $checkKel1->kecamatan->kota_kab->provinsi->id;
                        $namaProv = $checkKel1->kecamatan->kota_kab->provinsi->nama_provinsi;
                    } else {
                        $checkKel2 = $this->checkKel($x, $y, true);
                        if ($checkKel2) {
                            $kelId = $checkKel2->id;
                            $namaKel = $checkKel2->nama;
                            $kecId = $checkKel2->kecamatan->id;
                            $namaKec = $checkKel2->kecamatan->nama;
                            $kabId = $checkKel2->kecamatan->kota_kab->id;
                            $namaKab = $checkKel2->kecamatan->kota_kab->nama;
                            $provId = $checkKel2->kecamatan->kota_kab->provinsi->id;
                            $namaProv = $checkKel2->kecamatan->kota_kab->provinsi->nama_provinsi;
                        }
                    }

                    $dataInput = array(
                        'id' => Str::orderedUuid(),
                        'date_hotspot' => $date_hotspot,
                        'x' => $x,
                        'y' => $y,
                        'sumber' => $sumber,
                        'sumber2' => $oriSumber,
                        'counter' => $counter,
                        'source' => $source,
                        'confidence' => $confidence,
                        'confidence_level' => $level,
                        'brightness' => $brightness,
                        'tipe' => $tipe,
                        'geom' => DB::raw("ST_GeomFromText('POINT(' || $x || ' ' || $y || ')',4326)"),
                        'provinsi_id' => $provId,
                        'provinsi' => $namaProv,
                        'kotakab_id' => $kabId,
                        'kabkota' => $namaKab,
                        'kecamatan_id' => $kecId,
                        'kecamatan' => $namaKec,
                        'kelurahan_id' => $kelId,
                        'desa' => $namaKel,
                        'kawasan' => $fungsiKawasan ? $fungsiKawasan->kawasan : null,
                        'nama_hti' => $fungsiKawasan ? $fungsiKawasan->nama_hti : null,
                        'nama_ha' => $fungsiKawasan ? $fungsiKawasan->nama_ha : null,
                        'nama_kebun' => $fungsiKawasan ? $fungsiKawasan->nama_kebun : null,
                        'fungsi_kawasan' => $petaKawasan ? $petaKawasan->fungsi : null,
                        'sk_kawasan' => $petaKawasan ? $petaKawasan->sk_kawasan : null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'deleted_at' => $deletedAt
                    );
                    $insertData[] = $dataInput;
                }
            }
        }
        if (count($insertData) > 0) {
            DB::table('hotspot_satelit')->insertOrIgnore($insertData);
        }
        activity()->enableLogging();
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}

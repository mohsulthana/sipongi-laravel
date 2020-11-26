<?php

namespace App\Imports\ReadHotspot;

use App\Jobs\ReadHotspot\UpdateResumeHotspot;
use App\Models\HotspotSatelit;
use App\Traits\ImportHotspot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;

class ImportHotspotModis implements ToCollection, WithChunkReading, WithStartRow, WithEvents
{
    use Importable, ImportHotspot;

    private $url;
    private $fileName;
    private $setting;

    public function __construct($fileName, $url, $setting)
    {
        $this->url = $url;
        $this->fileName = $fileName;
        $this->setting = $setting;
    }

    public function collection(Collection $rows)
    {
        activity()->disableLogging();
        $insertData = array();
        foreach ($rows as $col) {
            if (!$col->filter()->isEmpty()) {
                $x = (float) $col[1]; //longitude
                $y = (float) $col[0]; //latitude
                $date = $col[5]; // date format wajib Y-m-d
                $time = preg_replace("/^(.{2})(.{2})$/i", "$1:$2:00", $col[6]); // time 4 character konversi jadi 00:00:00/H:i:s
                $parseDate = Carbon::parse("$date $time");
                $date_hotspot = $parseDate->format('Y-m-d H:i:s');
                $confidence = (int) $col[8];
                $brightness = (float) $col[2];
                $sumber = 'NASA-MODIS';
                $source = 'nasa/modis';
                $level = 'low';

                if ($confidence <= 29) {
                    $level = 'low';
                } else if ($confidence >= 30 && $confidence <= 80) {
                    $level = 'medium';
                } else if ($confidence >= 81) {
                    $level = 'high';
                }

                $count = HotspotSatelit::query()
                    ->where('date_hotspot', $date_hotspot)
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
                        'counter' => $counter,
                        'source' => $source,
                        'confidence' => $confidence,
                        'confidence_level' => $level,
                        'brightness' => $brightness,
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
            dispatch((new UpdateResumeHotspot($source, $this->setting))->onQueue('hotspot'))->delay(now()->addMinutes(1));
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

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                $exists = Storage::disk('privateNas')->exists("download/hotspot/modis/$this->fileName");
                if ($exists) {
                    Storage::disk('privateNas')->delete("download/hotspot/modis/$this->fileName");
                }
            }
        ];
    }
}

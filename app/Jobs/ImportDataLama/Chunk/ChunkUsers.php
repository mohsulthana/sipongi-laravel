<?php

namespace App\Jobs\ImportDataLama\Chunk;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ChunkUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $datas;
    private $dataReg;
    private $dataProv;
    private $dataDaops;
    private $dataRole;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($datas, $dataReg, $dataProv, $dataDaops, $dataRole)
    {
        $this->datas = $datas;
        $this->dataReg = $dataReg;
        $this->dataProv = $dataProv;
        $this->dataDaops = $dataDaops;
        $this->dataRole = $dataRole;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $keyDaops = array(
            4 => 'KTP-04',
            5 => 'PLK-05',
            6 => 'KPS-05',
            7 => 'TNL-07',
            8 => 'TNB-07',
            9 => 'PTN-05',
            10 => 'SKW-06',
            11 => 'SNT-07',
            12 => 'BJR-01',
            13 => 'PSR-02',
            14 => 'PKB-01',
            15 => 'BTM-02',
            16 => 'SIK-03',
            17 => 'DMI-04',
            18 => 'RNG-05',
            19 => 'GWA-01',
            20 => 'MLI-02',
            21 => 'TNG-03',
            22 => 'BTG-01',
            23 => 'LBB-02',
            24 => 'PMS-03',
            26 => 'D01',
            28 => 'D09',
            29 => 'D10',
            30 => 'D11',
            31 => 'D12',
            32 => 'D13',
            33 => 'D14',
            35 => 'D16',
            36 => 'D21',
            37 => 'D23',
            38 => 'D24',
            39 => 'D33',
            40 => 'D34',
            41 => 'BAN-06',
            42 => 'D35',
            43 => 'D36'
        );

        activity()->disableLogging();
        $data = array();
        $roleData = array();
        foreach ($this->datas as $val) {
            $checkReg = $this->dataReg->where('id', $val->regional_id)->first();
            $regId = $checkReg ? $val->regional_id : null;
            $checkProv = $this->dataProv->where('old_id', $val->provinsi_id)->first();
            $provId = $checkProv ? $checkProv->id : null;

            $daopsId = null;
            $kodeDaops = Arr::get($keyDaops, $val->daops_id, false);
            if ($kodeDaops) {
                $checkDaops = $this->dataDaops->where('kode_daops', $kodeDaops)->first();
                $daopsId = $checkDaops ? $checkDaops->id : null;
            }

            $id = Str::orderedUuid();
            $data[] = array(
                'id' => $id,
                'username' => $val->user_login,
                'name' => $val->user_name,
                'email' => $val->email,
                'password' => Hash::make('Sipongi2020!'),
                'default_pass' => true,
                'regional_id' => $regId,
                'provinsi_id' => $provId,
                'daops_id' => $daopsId,
                'unit_kerja' => $val->unit_kerja,
                'keterangan' => $val->keterangan,
                'is_super_admin' => $val->user_tipe === 'admin' ? true : false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            );

            if ($val->user_tipe !== 'admin') {
                if ($val->user_tipe === 'operator') {
                    $checkRole = $this->dataRole->where('name', 'operator')->first();
                } else {
                    $checkRole = $this->dataRole->where('name', 'pengguna')->first();
                }
                $roleData[] = array(
                    'role_id' => $checkRole->id,
                    'model_type' => User::class,
                    'model_uuid' => $id
                );
            }
        }

        DB::table('users')->insertOrIgnore($data);
        DB::table('model_has_roles')->insertOrIgnore($roleData);

        DB::delete(
            'DELETE FROM model_has_roles WHERE model_uuid IN ( 
                SELECT id FROM ( 
                    SELECT id, row_number() OVER w as rnum FROM users 
                    WINDOW w AS ( 
                        PARTITION BY username ORDER BY id 
                    )
                ) t 
            WHERE t.rnum > 1)'
        );

        DB::delete(
            'DELETE FROM users WHERE id IN ( 
                SELECT id FROM ( 
                    SELECT id, row_number() OVER w as rnum FROM users 
                    WINDOW w AS ( 
                        PARTITION BY username ORDER BY id 
                    )
                ) t 
            WHERE t.rnum > 1)'
        );

        activity()->enableLogging();
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:users', 'users:insert-data'];
    }
}

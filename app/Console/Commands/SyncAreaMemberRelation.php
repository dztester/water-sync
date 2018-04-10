<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncAreaMemberRelation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'water-sync:sync-area-member-relation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步DMA分区和会员关系数据';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = DB::table('hycsk.view_cust_meter')->count();
        $limit = 100;
        $times = ceil($count / $limit);

        for ($i = 0; $i < $times; $i++) {

            $offset = $i * $limit;

            $records = DB::table('hycsk.view_cust_meter')->limit($limit)->offset($offset)->get();

            $data_list = [];

            foreach ($records as $record) {
                dump($record, $record->yhid, $record->pqmc);
                $data_list[] = [
                    'member_number' => $record->yhid ?? '',
                    'area_name'     => $record->pqmc ?? '',
                ];
            }
            exit;

            $client = new Client(['base_uri' => 'http://182.61.56.51/']);
            $response = $client->request('POST', 'area_member_relation', [
                'json' => $data_list,
            ]);
        }
    }
}

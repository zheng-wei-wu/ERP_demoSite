<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

class ServerReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DB:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check inquiry list';

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
     * @return int
     */
    public function handle()
    {
      date_default_timezone_set("Asia/Taipei");

      /// read update time
      $path = storage_path()."/json/updateTime.json";
      $data = json_decode(file_get_contents($path), true);
      $updateTime = $data['updateTime'];

      // store update time
      $data["updateTime"] = date('Y-m-d H:i:s');
      $kkk = $data["updateTime"];
      $newData = json_encode($data, JSON_PRETTY_PRINT);
      file_put_contents($path, stripslashes($newData));

      Log::info("Check update time : [{$updateTime} - {$kkk}]");

      $frq_updates = DB::table('InquiryDetailVendor')
                      ->whereRaw("ModifiedDate > '$updateTime'")
                      ->select('InquiryID')
                      ->groupBy('InquiryID')
                      ->get();

      if(!$frq_updates->isEmpty()){

        foreach ($frq_updates as $frq_update) {

          Log::info("New inquiry record to process : {$frq_update->InquiryID}");

          // Call web api
          $client = new \GuzzleHttp\Client();
          $res = $client->request('GET', "http://127.0.0.1/ERP_demoSite/public/api/frq/order/{$frq_update->InquiryID}");

          Log::info("Outcome : {$res->getBody()}");
          Log::info("");
        }

        return;
      }

      Log::info("Nothing to process");
      Log::info("");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Product;
use App\History;
use Carbon\Carbon;
use DirkGroenen\Pinterest\Pinterest;
use DB;
class PushPin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:pin {collectionId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Product From Shopify To Pinterest';

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
        // Get product to push
        $collectionId = $this->argument('collectionId');
        $product = Product::where('collection_id',$collectionId)
                            ->where('is_publish',0)
                            ->inRandomOrder()->first();
        // Get token for excute
        $boardId = env('PINTEREST_BOARD');

        $account = DB::table('accounts')->where('limit_remaining','>',0)
                                        ->orWhereDate('exp_time','<',Carbon::now())
                                        ->first();
        if($product == null || $account == null) die;
        $pinterest = new Pinterest(null, null);
        $pinterest->auth->setOAuthToken($account->token);
        // die;
        try{
            $pinInfo = $pinterest->pins->create(array(
                "note"          => $product->product_title,
                "image_url"     => $product->product_image,
                "link"          => $product->product_link,
                "board"         => $boardId
            ));
            $limitRemainging = $pinterest->getRateLimitRemaining();
            // Save history
            $history = new History;
            $history->product_id = $product->product_id;
            $history->pinterest_id = $pinInfo->id;
            $history->save();
            // Update status product
            $product->is_publish = 1;
            $product->save();
            // Update Account table
            $account->limit_remaining = $limitRemainging;
            if($limitRemainging == 0){
                DB::table('accounts')->where('id',$account->id)->update([
                    'limit_remaining' => 0,
                    'exp_time' => Carbon::now()->addHour()
                ]);
            } else {
                DB::table('accounts')->where('id',$account->id)->update([
                    'limit_remaining' => $limitRemainging
                ]);
            }
            DB::table('logs')->insert([
                'message' => " Push Success: ". $product->product_id
            ]);

        } catch(\Exception $e){
            $expCode = $e->getCode();
            if($expCode == 429) {
                DB::table('accounts')->where('id',$account->id)
                                ->update([
                                    'limit_remaining' => 0,
                                    'exp_time' => Carbon::now()->addHour()
                                ]);
            }
            DB::table('logs')->insert([
                'message' => $e->getCode()
            ]);
        }
    }
}

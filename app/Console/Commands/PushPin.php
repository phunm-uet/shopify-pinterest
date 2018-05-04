<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Product;
use App\History;
use seregazhuk\PinterestBot\Factories\PinterestBot;
use Carbon\Carbon;
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
        $collectionId = $this->argument('collectionId');
        $product = Product::where('collection_id',$collectionId)
                            ->where('is_publish',0)
                            ->inRandomOrder()->first();
        $bot = PinterestBot::create();
        $username = env('PINTEREST_USERNAME');
        $password = env('PINTEREST_PASSWORD');
        $boardId = env('PINTEREST_BOARD');
        $result = $bot->auth->login($username, $password);
        if(!$result){
            echo $bot->getLastError();
            die;
        }   
        $pinInfo = $bot->pins->create($product->product_image, $boardId, $product->product_title,$product->product_link);
        if(!isset($pinInfo['id'])){
            DB::table('logs')->insert([
                'message' => $bot->getLastError()
            ]);
            die;
        }
        $pinLink = $pinInfo['id'];
        // Save history
        $history = new History;
        $history->product_id = $product->product_id;
        $history->pinterest_id = $pinInfo['id'];
        $history->save();
        // Update status product
        $product->is_publish = 1;
        $product->save();
        DB::table('logs')->insert([
            'message' => " Push Success: ". $product->product_id
        ]);
    }
}

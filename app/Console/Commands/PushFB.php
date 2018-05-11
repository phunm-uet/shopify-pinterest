<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Product;
use App\History;
use Carbon\Carbon;
use DB;
class PushFB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:fb {collectionId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push random product in collection to FB';

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
                            ->inRandomOrder()->first();
        $accessToken = env('FB_PAGE_TOKEN');
        if($product == null || $accessToken == null) die;
        $client = new Client();
        $body = [
            "access_token" => $accessToken,
            "caption" => $product['product_title'],
            "message" => $product['product_title'],
            "link" => $product['product_link'],
            "image" => $product['product_image']
        ];
        try{
            $r = $client->request('POST', 'https://graph.facebook.com/v3.0/me/feed', ['form_params' => $body]);
            $body = json_decode($r->getBody(),true);
            $postId = $body['id'];
            $product->is_publish = 1;
            $product->save();
            DB::table('logs')->insert([
                'message' => " Push FB Success: ". $postId
            ]);
        }catch(\Exception $e){
            DB::table('logs')->insert([
                'message' => $e->getMessage()
            ]);
        }
    }
}

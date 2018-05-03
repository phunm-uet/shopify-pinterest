<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Product;
use App\Collection;

class SyncProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Product From Shopify to DB';

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
        Product::truncate();
        $collections = Collection::all()->toArray();
        foreach($collections as $collection){
            $this->info('Loading Product in Collection '. $collection['collection_title']);
            $products = $this->getProductByCollection($collection['collection_id']);
            foreach($products as $product){
                $tmp = new Product();
                $tmp->product_id = $product['id'];
                $tmp->product_title = $product['title'];
                $tmp->product_image = $product['image']['src'];
                $tmp->product_link = env('SHOPIFY_URL')."/products/".$product['handle'];
                $tmp->collection_id = $collection['collection_id'];
                if($tmp->save()){
                    $this->info("Save success : " . $product['id']);
                }else {
                    $this->error("Save fail : " . $product['id']);
                }
            }
        }
    }

    public function getProductByCollection($collectionId){
        $products = [];
        $limit = 500;
        $shopifyURL = env('SHOPIFY_URL');
        $shopifyAPIKey = env('SHOPIFY_API_KEY');
        $shopifyAPISecret = env('SHOPIFY_API_SECRET');
        $config = array(
            'ShopUrl' => $shopifyURL,
            'ApiKey' => $shopifyAPIKey,
            'Password' => $shopifyAPISecret,
        );
        $shopify = new \PHPShopify\ShopifySDK($config);
        $products = $shopify->Product->get([
            'limit' => $limit,
            'fields' => 'id,handle,title,image',
            'collection_id' => $collectionId
        ]);
        return $products;
    }
}

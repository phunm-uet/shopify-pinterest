<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Collection;
class SyncCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:collection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Collection From Shopify to DB';

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
        $this->info('Getting collection From Shopify....');
        $shopifyURL = env('SHOPIFY_URL');
        $shopifyAPIKey = env('SHOPIFY_API_KEY');
        $shopifyAPISecret = env('SHOPIFY_API_SECRET');
        $config = array(
            'ShopUrl' => $shopifyURL,
            'ApiKey' => $shopifyAPIKey,
            'Password' => $shopifyAPISecret,
        );
        $shopify = new \PHPShopify\ShopifySDK($config);
        // customCollection
        $customCollection = $shopify->CustomCollection->get();
        // smartCollection
        $smartCollection = $shopify->SmartCollection->get();
        $collections = array_merge($customCollection,$smartCollection);
        foreach($collections as $collection){
            $this->info("Adding ".$collection['title']);
            $tmpCollection = Collection::where('collection_id',$collection['id'])->first();
            if($tmpCollection == null){
                $tmp = new Collection;
                $tmp->collection_id = $collection['id'];
                $tmp->collection_title = $collection['title'];
                $tmp->collection_link = env('SHOPIFY_URL')."/collections/".$collection['handle'];
                if($tmp->save()){
                    $this->info('Save Success');
                } else {
                    $this->error('Save Failed');
                }
            }
        }
    }
}

<?php

namespace App\Services\easymarket\ProductService;

use App\Enums\{DealEventActorType, DealEventEventType};
use App\Models\{Deal, DealEvent, Product, User};
use App\Services\easymarket\ProductService\Dtos\StoreCommand;
use App\Services\easymarket\ProductService\Exceptions\IncompleteSellerInfoException;
use App\Services\easymarket\ImageService\ImageServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;


class ProductService implements ProductServiceInterface
{

    /**
    * @var ImageServiceInterface
    */
    private $imageService;

    /**
     * @param  ImageServiceInterface  $imageService
     * @return void
     */
    public function __construct(
        ImageServiceInterface $imageService
    )
    {
        $this->imageService = $imageService;
    }

    /*
     * 商品取得
     * 
     * @return Collection<Product>
     */
    public function get(): Collection
    {
        return Product::orderBy('id', 'desc')->get();
    }

    /*
     * 商品出品処理
     * 
     * @param StoreCommand $storeCommand
     * @exception IncompleteSellerInfoException
     * @return Product
     */
    public function store(StoreCommand $storeCommand): Product
    {
        $seller = $storeCommand->seller;
        if (empty($seller->nickname)) {
            throw new IncompleteSellerInfoException();
        }

        $product = DB::transaction(function () use ($storeCommand) {
            $images = $this->imageService->saveUploadFiles($storeCommand->images);

            $product = Product::create([
                'name' => $storeCommand->name,
                'description' => $storeCommand->description,
                'price' => $storeCommand->price,
            ]);
            $product->save();

            $deal = new Deal();
            $deal->seller()->associate($storeCommand->seller);
            $deal->product()->associate($product);
            $deal->save();

            $product->images()->saveMany($images);

            $dealEvent = new DealEvent([
                'actor_type' => DealEventActorType::Seller,
                'event_type' => DealEventEventType::Listing,
            ]);
            $dealEvent->deal_eventable()->associate($storeCommand->seller);
            $deal->dealEvents()->save($dealEvent);

            return $product;
        });

        return $product;
    }

    /*
     * 購入商品一覧取得
     * 
     * @param User $user
     * @return Collection<Product>
     */
    public function getPurchasedProductsByUser(User $user): Collection
    {
        // $products = $user->dealsAsBuyer()->with('product')->get()->pluck('product');
        $products = $user->dealsAsBuyer()
                ->with(['product.images', 'product.deal']) // product.deal を追加してリレーションをロード
                ->get()
                ->map(function ($deal) {
                    $product = $deal->product;
                    if ($product) {
                        // Product に対応する最初の画像の URL を取得
                        $imageUrl = optional($product->images->first())->file_path;
                        // Deal のステータスを取得
                        $dealStatus = $deal->status;
                        // Product に画像 URL と Deal ステータスを追加
                        $product->image_url = $imageUrl;
                        $product->deal_status = $dealStatus;
                        return $product;
                    }
                })
                ->filter()
                ->values();
        return $products;
    }

    /*
     * 出品商品一覧取得
     * 
     * @param User $user
     * @return Collection<Product>
     */
    public function getListedProductsByUser(User $user): Collection
    {
        // $products = $user->dealsAsSeller()->with('product')->get()->pluck('product');
        $products = $user->dealsAsSeller()
                ->with(['product.images', 'product.deal']) // product.deal を追加してリレーションをロード
                ->get()
                ->map(function ($deal) {
                    $product = $deal->product;
                    if ($product) {
                        // Product に対応する最初の画像の URL を取得
                        $imageUrl = optional($product->images->first())->file_path;
                        // Deal のステータスを取得
                        $dealStatus = $deal->status;
                        // Product に画像 URL と Deal ステータスを追加
                        if (env('APP_ENV') === 'production') {
                            $product->image_url = env('AWS_URL') . $imageUrl;
                        } else {
                            $product->image_url = $imageUrl;
                        }
                        $product->deal_status = $dealStatus;
                        return $product;
                    }
                })
                ->filter()
                ->values();

        return $products;
    }
    


}
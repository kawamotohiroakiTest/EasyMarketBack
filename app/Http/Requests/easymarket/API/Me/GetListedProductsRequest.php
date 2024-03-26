<?php

namespace App\Http\Requests\easymarket\API\Me;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class GetListedProductsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // // ルートパラメータから商品IDを取得
        // $productId = $this->route('id');

        // // 商品IDが指定されていない場合は検証をスキップ
        // if (!$productId) {
        //     return true;
        // }

        // // 指定された商品を取得
        // $product = Product::find($productId);

        // // 商品が見つからない場合は検証をスキップ
        // if (!$product) {
        //     return true;
        // }

        // // 商品の出品者が現在ログインしているユーザーであるかを確認
        // return $product->seller_id === Auth::id();
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}

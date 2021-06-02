<?php

namespace App\Http\Controllers\Product;

use App\User;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use Symfony\Component\VarDumper\Cloner\Data;

class ProductBuyerTransactionController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $data['quantity'];

        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse('El vendedor debe ser diferente al comprador', 409);
        }

        if (!$buyer->isVerified()) {
            return $this->errorResponse("El comprador debe ser un usuario verificado", 409);
        }

        if (!$product->seller->isVerified()) {
            return $this->errorResponse("El vendedor debe ser un usuario verificado", 409);
        }

        if (!$product->isAvailable()) {
            return $this->errorResponse("El producto para esa transacción no está disponible", 409);
        }

        if ($product->quantity < $quantity) {
            return $this->errorResponse("El producto no tiene la cantidad disponible reuerida para esta trasacción", 409);
        }

        return
            DB::transaction(function () use($quantity, $product, $buyer) {
            $product->quantity -= $quantity;
            $product->save();

            $transaction = Transaction::create([
                'quantity' => $quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);

            return $this->showOne($transaction, 201);

        });

    }

}

<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Product;
use App\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $produscts = $seller->products;

        return $this->showAll($produscts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ]);

        $data['image'] = $request->file('image')->store('');
        $data['seller_id'] = $seller->id;
        $data['status'] = Product::PRODUCTO_NO_DISPONIBLE;

        $product = Product::create($data);

        return $this->showOne($product, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {

        $this->canBeUpdatedByThisSeller($product, $seller);

        $data = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'integer|min:1',
            'image' => 'image',
            'status' => 'in:'. Product::PRODUCTO_DISPONIBLE .',' . Product::PRODUCTO_NO_DISPONIBLE
        ]);

        if ($request->has('status')) {

            $product->status = $request->status;

            if ($product->isAvailable() && $product->categories()->count() === 0) {
                return $this->errorResponse(
                    'Un producto disponible debe tener al menos una categoria',
                    409
                );
            }

        }

        if ($request->hasFile('image')) {

            Storage::delete($product->image);

            $data['image'] = $data['image']->store('');

        }

        $product->fill($data);

        if ($product->isClean()) {
            return $this->errorResponse(
                'Especifica al menos un campo para actualizar',
                409
            );
        }

        $product->save();

        return $this->showOne($product);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->canBeUpdatedByThisSeller($product, $seller);
        Storage::delete($product->image);
        $product->delete();

        return $this->showOne($product);
    }

    /**
     * Check if a product can be upgraded by a specific seller.
     *
     * @param  \App\Seller  $seller
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    protected function canBeUpdatedByThisSeller(Product $product, Seller $seller){

        if ($product->seller_id != $seller->id) {

            throw new HttpException(422, 'El producto no le pertenece al vendedor');

        }
    }
}

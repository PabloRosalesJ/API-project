<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use App\Http\Controllers\ApiController;

class SellerBuyerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $buyers = $seller->products()
        ->whereHas('transaction')
        ->with('transaction.buyer')
        ->get()
        ->pluck('transaction')
        ->collapse()
        ->pluck('buyer')
        ->unique()
        ->values();

        return $this->showAll($buyers);
    }
}

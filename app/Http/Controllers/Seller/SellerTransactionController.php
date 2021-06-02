<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use App\Http\Controllers\ApiController;

class SellerTransactionController extends ApiController
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
        $transactions = $seller->products()
            ->whereHas('transaction')
            ->with('transaction')
            ->get()
            ->pluck('transaction')
            ->collapse();

        return $this->showAll($transactions);
    }
}

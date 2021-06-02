<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;

class CategoryBuyerController extends ApiController
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
    public function index(Category $category)
    {
        $buyers = $category->products()
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

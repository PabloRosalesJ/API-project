<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;

class CategoryTransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        $transaction = $category
            ->products()
            ->with('transaction')
            ->whereHas('transaction')
            ->get()
            ->pluck('transaction')
            ->collapse();

        return $this->showAll($transaction);
    }
}

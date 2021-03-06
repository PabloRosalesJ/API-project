<?php

use App\Category;
use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();

        DB::table('category_product')->truncate();

        User::flushEventListeners();
        Category::flushEventListeners();
        Product::flushEventListeners();
        Transaction::flushEventListeners();

        factory(User::class, 2000)->create();

        factory(Category::class, 30)->create();

        factory(Product::class, 1000)->create()->each(
            function ($product){
                $categories = Category::all()
                                ->random(random_int(1,5))
                                ->pluck('id');

                $product->categories()->attach($categories);
            }
        );

        factory(Transaction::class, 1000)->create();

        // $this->call(UsersTableSeeder::class);
    }
}

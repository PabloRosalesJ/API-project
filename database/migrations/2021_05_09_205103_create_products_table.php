<?php

use App\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->unsignedInteger('quantity');
            $table->string('status')->default(Product::PRODUCTO_DISPONIBLE);
            $table->string('image');
            //$table->unsignedInteger('seller_id');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            //$table->foreign('seller_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}

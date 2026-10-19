<?php

use Composer\Semver\Constraint\Constraint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function PHPSTORM_META\type;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 20);
            $table->string('image', 2048);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->Constrained()->restrict();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('origin')->nullable();
            $table->string('unit', 100);
            $table->string('image', 2048);
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('salle_price')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('version')->default(1);
            $table->boolean('active')->default(true);
            $table->boolean('featured')->default(false);
            $table->timestamps();
            $table->index(['active', 'categorie_id'], 'product_catalog_index');
        });

        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('fee')->nullable();
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code_promo', 50)->unique();
            $table->string('percentage')->nullable();
            $table->string('active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 50)->unique();
            $table->uuid('checkout_Token')->unique();
            $table->foreignId('delivery_zone_id')->Constrained()->restrict();
            $table->string('zone_name');
            $table->text('address')->nullable();
            $table->string('fulfillmentMode', 20);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('sub_total');
            $table->unsignedTinyInteger('discount_percentage')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('total');
            $table->string('statut', 20)->default("en_attente");
            $table->boolean('stock_applied')->default(false);
            $table->boolean('stock_restored')->default(false);
            $table->timestamp('canceled_at')->nullable();
            $table->index(['statut', 'created_at'], 'orders_statut_index');
            $table->timestamps();
        });

        Schema::create('orders_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->string('name');
            $table->string('unit', 100);
            $table->unsignedBigInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('line_total');
            $table->timestamps();
            $table->unique(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.  
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('delivery_zones');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('orders_items');
    }
};

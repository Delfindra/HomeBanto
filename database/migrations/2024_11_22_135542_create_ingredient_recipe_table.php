<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngredientRecipeTable extends Migration
{
    public function up()
    {
        Schema::create('ingredient_recipe', function (Blueprint $table) {
            $table->id(); // optional, if you want a unique ID for each row
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');  // recipe foreign key
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');  // ingredient foreign key
            $table->timestamps(); // optional, to track when the pivot row was created or updated
        });
    }

    public function down()
    {
        Schema::dropIfExists('ingredient_recipe');
    }
}

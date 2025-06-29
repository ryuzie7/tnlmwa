<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetsTable extends Migration
{
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('property_number')->unique();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('type')->nullable();
            $table->enum('condition', ['Good', 'Fair', 'Poor'])->default('Good');
            $table->string('location')->nullable();
            $table->string('building_name')->nullable();
            $table->string('fund')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->year('acquired_at')->nullable();
            $table->string('previous_custodian')->nullable();
            $table->string('custodian')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
}

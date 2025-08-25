<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('doc')->nullable();
            $table->tinyinteger('status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('category_id');
            $table->string('name');
            $table->string('url');
            $table->json('config')->nullable();
            $table->json('doc')->nullable();
            $table->tinyinteger('status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('websites_source', function (Blueprint $table) {
            $table->id();
            $table->integer('website_id');
            $table->integer('source_id');
            $table->json('doc')->nullable();
            $table->tinyinteger('status_id')->default(1);
            $table->timestamps();
        });


        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('doc')->nullable();
            $table->tinyinteger('status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->string('name');
            $table->string('url');
            $table->json('doc')->nullable();
            $table->tinyinteger('status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('doc')->nullable();
            $table->tinyinteger('status_id')->default(1);
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
        Schema::dropIfExists('websites');
        Schema::dropIfExists('websites_source');

        Schema::dropIfExists('categories');
        Schema::dropIfExists('sources');
        Schema::dropIfExists('networks');
    }
};

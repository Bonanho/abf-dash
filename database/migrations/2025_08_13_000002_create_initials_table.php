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

        Schema::create('websites_sources', function (Blueprint $table) {
            $table->id();
            $table->integer('website_id');
            $table->integer('source_id');
            $table->json('doc')->nullable();
            $table->tinyinteger('status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('websites_posts_queue', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer('website_id');
            $table->integer('website_source_id');
            $table->integer('source_id');
            $table->bigInteger('source_post_id');
            $table->tinyinteger('type_id');
            $table->json('doc')->nullable();
            $table->tinyinteger('status_id')->default(0);
            $table->timestamps();
        });

        Schema::create('websites_posts', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger('website_post_queue_id');
            $table->integer('website_id');
            $table->integer('website_source_id');
            $table->integer('source_id');
            $table->bigInteger('source_post_id');
            $table->string('post_title');
            $table->longText('post_description');
            $table->longText('post_content');
            $table->string('post_image',512);
            $table->string('post_image_caption');
            $table->string('post_category');
            $table->json('seo_data');
            $table->string('url_original');
            $table->bigInteger('website_post_id')->nullable();
            $table->bigInteger('website_image_id')->nullable();
            $table->string('website_post_url')->nullable();
            $table->tinyinteger('status_id')->default(0);
            $table->timestamps();
        });

        

        Schema::create('aux_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('doc')->nullable();
            $table->tinyinteger('status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('aux_networks', function (Blueprint $table) {
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
            $table->integer('type_id')->default(1);
            $table->json('template')->nullable();
            $table->json('doc')->nullable();
            $table->tinyinteger('status_id')->default(0);
            $table->timestamps();
        });

        Schema::create('sources_posts', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer('source_id');
            $table->bigInteger('post_origin_id');
            $table->string('endpoint');
            $table->json('doc')->nullable(); 
            $table->json('post_data')->nullable(); 
            $table->json('error')->nullable();
            $table->tinyinteger('status_id')->default(0);
            $table->timestamps();
        });



        Schema::create('adtags', function (Blueprint $table) {
            $table->id();
            $table->integer('website_id');
            $table->string('name');
            $table->json('doc')->nullable(); 
            $table->tinyinteger('status_id')->default(1);
            $table->timestamps();
        });
        Schema::create('adtags_adunits', function (Blueprint $table) {
            $table->id();
            $table->integer('website_id');
            $table->integer('adtag_id');
            $table->integer('network_id');
            $table->string('name');
            $table->json('doc')->nullable(); 
            $table->tinyinteger('status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('fin_deliveries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('network_id');
            $table->string('adunit');
            $table->string('prints');
            $table->string('revenue');
            $table->tinyinteger('status_id')->default(0);
            $table->timestamps();
        });
        
        Schema::create('fin_incomes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('website_id');
            $table->integer('network_id');
            $table->string('prints');
            $table->string('revenue');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
        Schema::dropIfExists('websites');
        Schema::dropIfExists('websites_sources');
        Schema::dropIfExists('websites_posts_queue');
        Schema::dropIfExists('websites_posts');

        Schema::dropIfExists('aux_categories');
        Schema::dropIfExists('aux_networks');

        Schema::dropIfExists('sources');
        Schema::dropIfExists('sources_posts');

        Schema::dropIfExists('adtags');
        Schema::dropIfExists('adtags_adunits');
        Schema::dropIfExists('fin_deliveries');
        Schema::dropIfExists('fin_incomes');
    }
};

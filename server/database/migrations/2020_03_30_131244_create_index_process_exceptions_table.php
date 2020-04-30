<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexProcessExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('index_process_exceptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fetched_entity_name')->nullable();
            $table->string('refined_fetched_entity_name')->nullable();
            $table->text('fetched_entity_address')->nullable();
            $table->string('fetched_state',15)->nullable();
            $table->string('bucket_name');
            $table->text('object_name');
            $table->tinyInteger('mannual_indexed')->default(0); 
            $table->text('entered_entity_name')->nullable();
            $table->date('received_at')->nullable();
            $table->date('process_at')->nullable();
            $table->timestamps();
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('index_process_exceptions');
    }
}

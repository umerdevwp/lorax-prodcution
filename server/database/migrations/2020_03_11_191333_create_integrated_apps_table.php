<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntegratedAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integrated_apps', function (Blueprint $table) {
            $table->bigIncrements('app_id');
            $table->string('app_name');
            $table->unsignedInteger('created_by_id')->nullable($value = true);
            $table->ipAddress('created_by_ip')->nullable($value = true);
            $table->unsignedInteger('updated_by_id')->nullable($value = true);
            $table->ipAddress('updated_by_ip')->nullable($value = true);
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
        Schema::dropIfExists('integrated_apps');
    }
}

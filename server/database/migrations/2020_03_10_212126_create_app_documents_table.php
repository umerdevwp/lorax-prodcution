<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_documents', function (Blueprint $table) {
            $table->uuid('document_id')->primary();
            $table->unsignedBigInteger('app_id');
            $table->text('document_path')->nullable($value = true);
            $table->string('bucket_name')->nullable($value = true);
            $table->string('encryption_key',150)->nullable($value = true);
            $table->string('encryptionsha256_key',150)->nullable($value = true);
            $table->enum('is_public',['Y','N'])->default('N');
            $table->date('received_at')->nullable($value = true);
            $table->date('process_at')->nullable($value = true);
            $table->integer('update_by_id')->nullable($value = true);
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
        Schema::dropIfExists('app_documents');
    }
}

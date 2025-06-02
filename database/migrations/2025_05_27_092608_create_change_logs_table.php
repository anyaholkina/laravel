<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeLogsTable extends Migration
{
    public function up()
    {
        Schema::create('change_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity');                  
            $table->unsignedBigInteger('entity_id');  
            $table->json('before')->nullable();       
            $table->json('after')->nullable();       
            $table->string('action');                 
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->timestamps();

            $table->index(['entity', 'entity_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('change_logs');
    }
}

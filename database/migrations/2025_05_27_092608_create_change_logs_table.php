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
            $table->string('entity');                  // user, role, permission
            $table->unsignedBigInteger('entity_id');  // ID изменяемой записи
            $table->json('before')->nullable();       // данные до изменения
            $table->json('after')->nullable();        // данные после изменения
            $table->string('action');                  // create, update, delete
            $table->unsignedBigInteger('user_id')->nullable(); // кто изменил
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

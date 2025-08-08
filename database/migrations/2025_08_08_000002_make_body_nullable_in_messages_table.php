<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeBodyNullableInMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->text('body')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->text('body')->nullable(false)->change();
        });
    }
}

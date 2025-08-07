<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateConversationsForeignKeys extends Migration
{
    public function up()
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign(['user1_id']);
            $table->dropForeign(['user2_id']);

            // Re-add with cascade on delete
            $table->foreign('user1_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user2_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['user1_id']);
            $table->dropForeign(['user2_id']);

            // Re-add without cascade
            $table->foreign('user1_id')->references('id')->on('users');
            $table->foreign('user2_id')->references('id')->on('users');
        });
    }
}

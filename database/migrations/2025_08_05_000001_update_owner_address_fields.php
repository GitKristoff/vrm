<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOwnerAddressFields extends Migration
{
    public function up()
    {
        Schema::table('owners', function (Blueprint $table) {
            // Drop the old address column
            $table->dropColumn('address');

            // Add new address fields
            $table->string('street')->after('phone');
            $table->string('barangay')->after('street');
            $table->string('municipality')->after('barangay');
            $table->string('province')->after('municipality');
            $table->string('region')->after('province');
            $table->string('country')->default('Philippines')->after('region');
        });
    }

    public function down()
    {
        Schema::table('owners', function (Blueprint $table) {
            // Remove new address fields
            $table->dropColumn(['street', 'barangay', 'municipality', 'province', 'region', 'country']);

            // Restore the old address column
            $table->string('address')->after('phone');
        });
    }
}

<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-16

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('balance', 14, 2)->default(0)->after('email');
            $table->enum('role', ['buyer', 'seller', 'admin'])->default('buyer')->after('name');
        });
    }
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('balance');
            $table->dropColumn('role');
        });
    }
};

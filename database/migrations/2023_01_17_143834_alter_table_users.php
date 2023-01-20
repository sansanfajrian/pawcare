<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\DB;

class AlterTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */


    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->enum('gender', ['Laki-Laki', 'Perempuan'])->default('Laki-Laki');
            $table->text('Alamat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->string('username');
        $table->dropColumn('gender');
        $table->dropColumn('Alamat');
    }
}

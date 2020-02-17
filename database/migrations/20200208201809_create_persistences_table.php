<?php

declare(strict_types=1);

use App\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreatePersistencesTable.
 */
class CreatePersistencesTable extends Migration
{
    public function up()
    {
        $this->schema->create('persistences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('code')->unique();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        $this->schema->drop('persistences');
    }
}

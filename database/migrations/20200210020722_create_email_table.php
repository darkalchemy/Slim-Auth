<?php

declare(strict_types=1);

use App\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateEmailTable.
 */
class CreateEmailTable extends Migration
{
    public function up()
    {
        $this->schema->create('email', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->tinyInteger('priority')->unsigned()->default(10)->index();
            $table->tinyInteger('sent')->unsigned()->default(0)->index();
            $table->integer('cancelled')->unsigned()->default(0);
            $table->integer('send_count')->unsigned()->default(0);
            $table->integer('error_count')->unsigned()->default(0);
            $table->text('subject');
            $table->longText('body');
            $table->dateTime('date_sent')->nullable()->index();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        $this->schema->drop('email');
    }
}

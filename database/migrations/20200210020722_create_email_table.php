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
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('priority')->default(10)->index();
            $table->unsignedTinyInteger('sent')->default(0)->index();
            $table->unsignedInteger('cancelled')->default(0);
            $table->unsignedInteger('send_count')->default(0);
            $table->unsignedInteger('error_count')->default(0);
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

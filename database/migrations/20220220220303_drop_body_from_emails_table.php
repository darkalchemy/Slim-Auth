<?php

declare(strict_types=1);

use App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class DropBodyFromEmailsTable.
 */
class DropBodyFromEmailsTable extends Migration
{
    public function up()
    {
        $this->schema->table('email', function (Blueprint $table) {
            $table->dropColumn('body');
        });
    }

    public function down()
    {
    }
}

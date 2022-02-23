<?php

declare(strict_types=1);

use App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class AddUriColumnToEmailsTable.
 */
class AddUriColumnToEmailsTable extends Migration
{
    public function up()
    {
        $this->schema->table('email', function (Blueprint $table) {
            $table->string('uri')->after('subject');
        });
    }

    public function down()
    {
    }
}

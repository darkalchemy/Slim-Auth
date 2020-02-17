<?php

declare(strict_types=1);

namespace App\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;
use Phinx\Migration\AbstractMigration;

/**
 * Class Migration.
 */
class Migration extends AbstractMigration
{
    protected Builder $schema;

    protected function init()
    {
        parent::init();
        $this->schema = Capsule::schema();
    }
}

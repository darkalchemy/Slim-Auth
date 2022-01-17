<?php

declare(strict_types=1);

namespace App\Migration;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;
use Phinx\Migration\AbstractMigration;

/**
 * Class Migration.
 */
class Migration extends AbstractMigration
{
    protected Builder $schema;

    public function init()
    {
        $this->schema = Capsule::schema();
    }
}

<?php

declare(strict_types=1);

use App\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateRolesTable.
 */
class CreateRolesTable extends Migration
{
    public function up()
    {
        $this->schema->create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('permissions')->nullable();
            $table->timestamps();
        });

        $this->execute(
            "INSERT INTO roles (name, slug, created_at) VALUES
                    ('User', 'user', NOW()),
                    ('Power User', 'power_user', NOW()),
                    ('Super User', 'super_user', NOW()),
                    ('Donor', 'donor', NOW()),
                    ('Uploader', 'uploader', NOW()),
                    ('Coder', 'coder', NOW()),
                    ('Editor', 'editor', NOW()),
                    ('Support', 'support', NOW()),
                    ('Moderator', 'moderator', NOW()),
                    ('Super Moderator', 'super_moderator', NOW()),
                    ('Administrator', 'administrator', NOW()),
                    ('Sysop', 'sysop', NOW()),
                    ('Owner', 'owner', NOW())"
        );
    }

    public function down()
    {
        $this->schema->drop('roles');
    }
}

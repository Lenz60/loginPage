<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserToken extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'date_created' => [
                'type' => 'INT',
                'null' => false,
            ],


        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user_token');
    }

    public function down()
    {
        $this->forge->dropTable('user_token');
    }
}

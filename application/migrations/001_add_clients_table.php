<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_clients_table extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => '8',
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
			),
			'gender' => array(
				'type' => 'VARCHAR',
				'constraint' => '6',
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			)
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('clients');
	}

	public function down()
	{
		$this->dbforge->drop_table('clients');
	}
}

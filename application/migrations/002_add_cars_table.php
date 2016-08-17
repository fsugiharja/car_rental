<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_cars_table extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => '8',
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'brand' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
			),
			'year' => array(
				'type' => 'INT',
				'constraint' => '10',
			),
			'color' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
			),
			'plate' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
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
		$this->dbforge->create_table('cars');
	}

	public function down()
	{
		$this->dbforge->drop_table('cars');
	}
}

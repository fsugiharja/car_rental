<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rental_table extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => '8',
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'car_id' => array(
				'type' => 'INT',
				'constraint' => '8',
			),
			'client_id' => array(
				'type' => 'INT',
				'constraint' => '8',
			),
			'date_from' => array(
				'type' => 'DATE',
			),
			'date_to' => array(
				'type' => 'DATE',
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
		$this->dbforge->create_table('rentals');
	}

	public function down()
	{
		$this->dbforge->drop_table('rentals');
	}
}

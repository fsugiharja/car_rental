<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class m_cars extends MY_Model {
	protected $_table = 'cars';
	
	/**
     * Fetch all the records in the table. Can be used as a generic call
     * to $this->_database->get() with scoped methods.
     */
    public function get_all()
    {
        $this->trigger('before_get');

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }
		
		$this->_database->select("brand, type, year, color, plate");
        $result = $this->_database->get($this->_table)
						->{$this->_return_type(1)}();
        $this->_temporary_return_type = $this->return_type;

        foreach ($result as $key => &$row)
        {
            $row = $this->trigger('after_get', $row, ($key == count($result) - 1));
        }

        $this->_with = array();
        return $result;
    }
	
    /**
     * Fetch a single record based on the primary key. Returns an object.
     */
    public function get($primary_value)
    {
		$this->_database->select("id, brand, type, plate");
		return $this->get_by($this->primary_key, $primary_value);
    }
}


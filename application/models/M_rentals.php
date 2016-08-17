<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class m_rentals extends MY_Model {
	protected $_table = 'rentals';
	
	/**
     * Fetch all the records in the table. Can be used as a generic call
     * to $this->_database->get() with scoped methods.
     */
    public function get_all_custom()
    {
        $this->trigger('before_get');

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }
		
		$this->_database->select(array(
			"clients.name", 
			"cars.brand", "cars.type", "cars.plate", 
			"rentals.date_from as date-from", "rentals.date_to as date-to", 
		));
		$this->_database->join("clients", "clients.id = rentals.client_id", "left");
		$this->_database->join("cars", "cars.id = rentals.car_id", "left");
		$this->_database->order_by("rentals.date_from", "ASC");
		
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
     * get rental history by client_id
     */
    public function get_histories_client($id)
    {
        $this->trigger('before_get');

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }
		
		$this->_database->select(array(
			"cars.brand", "cars.type", "cars.plate", 
			"rentals.date_from as date-from", "rentals.date_to as date-to", 
		));
		$this->_database->join("clients", "clients.id = rentals.client_id", "left");
		$this->_database->join("cars", "cars.id = rentals.car_id", "left");
		$this->_database->where("clients.id", $id);
		$this->_database->order_by("rentals.date_from", "ASC");
		
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
     * get rental history by car_id & month
     */
    public function get_histories_car($id, $month)
    {
        $this->trigger('before_get');

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }
		
		$this->_database->select(array(
			"clients.name as rent-by", 
			"rentals.date_from as date-from", "rentals.date_to as date-to", 
		));
		$this->_database->join("clients", "clients.id = rentals.client_id", "left");
		$this->_database->join("cars", "cars.id = rentals.car_id", "left");
		$this->_database->where("cars.id", $id);
		$this->_database->where("DATE_FORMAT(date_from,'%m-%Y') = '".$month."' OR DATE_FORMAT(date_to,'%m-%Y') = '".$month."'");
		$this->_database->order_by("rentals.date_from", "ASC");
		
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
     * get rented car by specified date
     */
    public function get_rented_car($date)
    {
		$myDateTime = DateTime::createFromFormat('d-m-Y', $date);
		$newDateString = $myDateTime->format('Y-m-d');
        $this->trigger('before_get');

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }
		
		$this->_database->select(array(
			"cars.brand", "cars.type", "cars.plate",  
		));
		$this->_database->join("clients", "clients.id = rentals.client_id", "left");
		$this->_database->join("cars", "cars.id = rentals.car_id", "left");
		$this->_database->where("date_from <= '".$newDateString."' AND date_to >= '".$newDateString."'");
		$this->_database->order_by("rentals.date_from", "ASC");
		
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
     * get rented car ids by specified date
     */
    public function get_rented_car_ids($date)
    {
		$myDateTime = DateTime::createFromFormat('d-m-Y', $date);
		$newDateString = $myDateTime->format('Y-m-d');
        $this->trigger('before_get');

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }
		
		$this->_database->select(array(
			"cars.id",  
		));
		$this->_database->join("cars", "cars.id = rentals.car_id", "left");
		$this->_database->where("date_from <= '".$newDateString."' AND date_to >= '".$newDateString."'");
		$this->_database->order_by("rentals.date_from", "ASC");
		
        $result = $this->_database->get($this->_table)
						->{$this->_return_type(1)}();
        $this->_temporary_return_type = $this->return_type;
		
        foreach ($result as $key => &$row)
        {
            $row = $this->trigger('after_get', $row, ($key == count($result) - 1));
        }

        $this->_with = array();
		$rented_car_ids = array();
		foreach($result as $car){
			if(!in_array($car->id, $rented_car_ids)){
				$rented_car_ids[] = $car->id;
			}
		}
        return $rented_car_ids;
    }
}


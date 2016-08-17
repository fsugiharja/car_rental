<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Cars extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
    }
	
	/*
	* check is plate already exist in database
	*/
	private function is_exist_plate($plate, $id = 0){
		$cars = $this->m_cars->get_many_by(array("plate" => $plate, "id !=" => $id));
		return (count($cars) > 0 ? 1 : 0);
	}
	
	/*
	* check is valid year, can't future year
	*/
	private function is_valid_year($year){
		return (date("Y") < $year ? false : true);
	}
	
	/*
	* regex check date format, must be DD-MM-YYYY
	*/
	private function check_date_format($month){
		if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/", $month)){
			return true;
		}else{
			return false;
		}
	}
	/**
	* create new cars
	* input: 
	*	data[brand]
	*	data[type]
	*	data[year]
	*	data[color]
	*	data[plate]
	* ounput:
	*	id
	*/
    public function index_post()
    {
        $data = $this->post('data');
		$this->load->model("m_cars");
        
		if (!is_array($data) || count($data) == 0){
			$this->set_response([
                'message' => sprintf($this->lang->line("required_label"), "Data Car")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		
		$error_message = array();
        if (empty($data["brand"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[brand]");
		}
		
        if (empty($data["type"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[type]");
		}
		
        if (empty($data["year"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[year]");
		} else {
			if(!$this->is_valid_year($data["year"])){
				$error_message[] = $this->lang->line("invalid_year_label");
			}
		}
		
        if (empty($data["color"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[color]");
		}
		
        if (empty($data["plate"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[plate]");
		} else {
			if($this->is_exist_plate($data["plate"])){
				$error_message[] = sprintf($this->lang->line("exist_plate_label"), $data["plate"]);
			}
		}
		
		$car_id = 0;
		if(count($error_message) > 0){
			// Set the response and exit
            $this->response([
                'message' => (count($error_message) == 1 ? $error_message[0] : implode(", ", $error_message))
            ], REST_Controller::HTTP_NOT_FOUND);
			return;
		} else {
			$data_input = array(
				"brand" => $data["brand"],
				"type" => $data["type"],
				"year" => $data["year"],
				"color" => $data["color"],
				"plate" => $data["plate"],
				"created_at" => date("Y-m-d H:i:s"),
			);
			$car_id = $this->m_cars->insert($data_input);
		}
		
        if ($car_id){
            $this->set_response([
                'id' => $car_id,
            ], REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("create_failed"), "Car")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
	
	/**
	* update cars
	* input: 
	*	data[brand]
	*	data[type]
	*	data[year]
	*	data[color]
	*	data[plate]
	* ounput:
	*/
    public function index_put($car_id = 0)
    {
		$car_id = (int)$car_id;
        $data = $this->put('data');
		$this->load->model("m_cars");
        
		$car_data = $this->m_cars->get($car_id);
		if(!$car_data){
			// Set the response and exit
			$this->response([
				'message' => sprintf($this->lang->line("notfound_label"), "Car ID")
			], REST_Controller::HTTP_NOT_FOUND);
			return;
		}
		
		if (!is_array($data) || count($data) == 0){
			$this->set_response([
                'message' => sprintf($this->lang->line("required_label"), "Data Car")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		
		$error_message = array();
        if (empty($data["brand"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[brand]");
		}
		
        if (empty($data["type"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[type]");
		}
		
        if (empty($data["year"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[year]");
		} else {
			if(!$this->is_valid_year($data["year"])){
				$error_message[] = $this->lang->line("invalid_year_label");
			}
		}
		
        if (empty($data["color"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[color]");
		}
		
        if (empty($data["plate"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[plate]");
		} else {
			if($this->is_exist_plate($data["plate"], $car_id)){
				$error_message[] = sprintf($this->lang->line("exist_plate_label"), $data["plate"]);
			}
		}
		
		$is_updated = false;
		if(count($error_message) > 0){
			// Set the response and exit
            $this->response([
                'message' => (count($error_message) == 1 ? $error_message[0] : implode(", ", $error_message))
            ], REST_Controller::HTTP_NOT_FOUND);
			return;
		} else {
			$data_input = array(
				"brand" => $data["brand"],
				"type" => $data["type"],
				"year" => $data["year"],
				"color" => $data["color"],
				"plate" => $data["plate"],
				"updated_at" => date("Y-m-d H:i:s"),
			);
			$is_updated = $this->m_cars->update($car_id, $data_input);
		}
		
        if ($is_updated){
            $this->set_response(NULL,REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("edit_failed"), "Client")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }	
	
	/**
	* delete car
	* input: 
	*	id
	* ounput:
	*/
    public function index_delete($car_id = 0)
    {
		$car_id = (int) $car_id;
		
		$this->load->model("m_cars");
		
		$is_deleted = 0;
		$car_data = $this->m_cars->get($car_id);
		if($car_data){			
			$is_deleted = $this->m_cars->delete($car_id);
		} else {
			$this->response([
				'message' => sprintf($this->lang->line("notfound_label"), "Car ID")
			], REST_Controller::HTTP_NOT_FOUND);
			return;
		}
		
        if ($is_deleted){
            $this->set_response(NULL,REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("delete_failed"), "Car")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
	
	/**
	* get all cars
	* input: 
	* ounput:
	* 	array of cars
	*/
    public function index_get()
    {		
		$this->load->model("m_cars");
		$cars = $this->m_cars->get_all();
		
        if (is_array($cars)){
            $this->set_response($cars,REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("notfound_label"), "Car")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
	
	/**
	* retrieve which car is being rented on specified date 
	* input: 
	*	$date (dd-mm-yyyy)
	* ounput:
	* 	array of rentals
	*/
    public function rented_get($car_id = 0)
    {		
		$date = $this->get("date");
        
		if(!$date){
			$this->set_response([
                'message' => sprintf($this->lang->line("required_label"), "Date")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		
		$valid_date_format = $this->check_date_format($date);
		if(!$valid_date_format){
			$this->set_response([
                'message' => $this->lang->line("invalid_date_format_label")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;			
		}
		
		$this->load->model("m_rentals");
		$return_data = new stdClass();
		$return_data->date = $date;
		$return_data->rented_cars = $this->m_rentals->get_rented_car($date);
		
        if ($return_data){
            $this->set_response($return_data,REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("notfound_label"), "Rented Cars")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
	
	/**
	* retrieve information which car is free within specific date 
	* input: 
	*	$date (dd-mm-yyyy)
	* ounput:
	* 	array of rentals
	*/
    public function free_get($car_id = 0)
    {		
		$date = $this->get("date");
        
		if(!$date){
			$this->set_response([
                'message' => sprintf($this->lang->line("required_label"), "Date")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		
		$valid_date_format = $this->check_date_format($date);
		if(!$valid_date_format){
			$this->set_response([
                'message' => $this->lang->line("invalid_date_format_label")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;			
		}
		
		$this->load->model("m_rentals");
		$this->load->model("m_cars");
		$return_data = new stdClass();
		$return_data->date = $date;
		$return_data->free_cars = array();
		$get_rented_car_ids = $this->m_rentals->get_rented_car_ids($date);
		
		$condition = array();
		if(count($get_rented_car_ids) > 0){
			$condition = array(
				"id not in (".implode(", ", $get_rented_car_ids).")"
			);
		}
		$get_free_cars = $this->m_cars->get_many_by($condition);
		foreach($get_free_cars as $car){
			$return_data->free_cars[] = array(
				"brand" => $car->brand,
				"type" => $car->type,
				"plate" => $car->plate,
			);
		}
		
        if ($return_data){
            $this->set_response($return_data,REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("notfound_label"), "Rented Cars")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
}

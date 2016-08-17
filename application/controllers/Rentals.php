<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Rentals extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
    }
	
	/*
	* function to check if client is renting cars at selected rent date
	* input:
	*	client_id
	*	date_from
	*	date_to
	* output:
	*	(bool) true | false
	*/
	private function is_client_rent_car($client_id, $date_from, $date_to, $rental_id = 0){
		$condition = "id != ".$rental_id." AND ";
		$condition .= "client_id = ".$client_id." AND ";
		$condition .= "(";
		$condition .= "(date_from <= '".$date_from."' AND date_to >= '".$date_from."') OR ";
		$condition .= "(date_from <= '".$date_to."' AND date_to >= '".$date_to."')";
		$condition .= ")";
		$client_rents = $this->m_rentals->get_many_by($condition);
		
		return (count($client_rents) > 0 ? true : false);
	}
	
	/*
	* function to check if car is rented at selected rent date
	* input:
	*	car_id
	*	date_from
	*	date_to
	* output:
	*	(bool) true | false
	*/
	private function is_car_rented($car_id, $date_from, $date_to, $rental_id = 0){
		$condition = "id != ".$rental_id." AND ";
		$condition .= "car_id = ".$car_id." AND ";
		$condition .= "(";
		$condition .= "(date_from <= '".$date_from."' AND date_to >= '".$date_from."') OR ";
		$condition .= "(date_from <= '".$date_to."' AND date_to >= '".$date_to."')";
		$condition .= ")";
		$car_rented = $this->m_rentals->get_many_by($condition);
		return (count($car_rented) > 0 ? true : false);
	}
	
	/*
	* function to get different in days from 2 dates
	* input:
	*	date_from
	*	date_to
	* output:
	*	diff in days
	*/
	private function day_diff($date_from, $date_to){
		$now = strtotime($date_to);
		$your_date = strtotime($date_from);
		$datediff = $now - $your_date;
		return floor($datediff/(60*60*24));
	}
	
	/*
	* get max rent duration
	* output:
	*	max rent duration
	*/
	private function get_max_rent_duration(){
		return 2; // today + 2 day after 
	}
	
	/**
	* create new cars
	* input: 
	*	data[car-id]
	*	data[client-id]
	*	data[date-from]
	*	data[date-to]
	* ounput:
	*	id
	*/
    public function index_post()
    {
        $data = $this->post('data');
		$this->load->model("m_clients");
		$this->load->model("m_cars");
		$this->load->model("m_rentals");
        
		if (!is_array($data) || count($data) == 0){
			$this->set_response([
                'message' => sprintf($this->lang->line("required_label"), "Data Rental")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		
		$error_message = array();
        if (empty($data["car-id"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[car-id]");
		} else {
			$car = $this->m_cars->get($data["car-id"]);
			if(!$car){
				$this->set_response([
					'message' => sprintf($this->lang->line("notfound_label"), "Car ID")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
		}
		
        if (empty($data["client-id"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[client-id]");
		} else {
			$client = $this->m_clients->get($data["client-id"]);
			if(!$client){
				$this->set_response([
					'message' => sprintf($this->lang->line("notfound_label"), "Client ID")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
		}
		
        if (empty($data["date-from"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[date-from]");
		}
		
        if (empty($data["date-to"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[date-to]");
		}
		
		$rental_id = 0;
		if(count($error_message) > 0){
			// Set the response and exit
            $this->response([
                'message' => (count($error_message) == 1 ? $error_message[0] : implode(", ", $error_message))
            ], REST_Controller::HTTP_NOT_FOUND);
			return;
		} else {
			$date_from = date("Y-m-d", strtotime($data["date-from"]));
			$date_to = date("Y-m-d", strtotime($data["date-to"]));
			$rent_date = array(
				"from" => date("Y-m-d", strtotime("+1 day")),
				"to" => date("Y-m-d", strtotime("+7 day")),
			);
			
			$valid_rent_date = (
				($date_from >= $rent_date["from"] && $date_from <= $rent_date["to"])
				&&
				($date_to >= $rent_date["from"] && $date_to <= $rent_date["to"])
				? true : false
			);
			
			if($date_from > $date_to || !$valid_rent_date){
				$this->set_response([
					'message' => $this->lang->line("invalid_range_date_label")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
			
			$day_diff = $this->day_diff($date_from, $date_to);			
			if($day_diff > $this->get_max_rent_duration()){
				$this->set_response([
					'message' => $this->lang->line("rent_max_duration_label")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
			
			$client_id = (int) $data["client-id"];
			$is_client_rent_car = $this->is_client_rent_car($client_id, $date_from, $date_to);
			if($is_client_rent_car){
				$this->set_response([
					'message' => $this->lang->line("client_rent_car_label")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
			
			$car_id = (int) $data["car-id"];
			$is_car_rented = $this->is_car_rented($car_id, $date_from, $date_to);
			if($is_car_rented){
				$this->set_response([
					'message' => $this->lang->line("car_rented_label")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
			
			$data_input = array(
				"car_id" => $car_id,
				"client_id" => $client_id,
				"date_from" => $date_from,
				"date_to" => $date_to,
				"created_at" => date("Y-m-d H:i:s"),
			);
			$rental_id = $this->m_rentals->insert($data_input);
		}
		
        if ($rental_id){
            $this->set_response([
                'id' => $rental_id,
            ], REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("create_failed"), "Rental")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
	
	/**
	* update rental
	* input: 
	* input: 
	*	data[car-id]
	*	data[client-id]
	*	data[date-from]
	*	data[date-to]
	* ounput:
	*/
    public function index_put($rental_id = 0)
    {
		$rental_id = (int)$rental_id;
        $data = $this->put('data');
		$this->load->model("m_clients");
		$this->load->model("m_cars");
		$this->load->model("m_rentals");
        
        
		$rental_data = $this->m_rentals->get($rental_id);
		if(!$rental_data){
			// Set the response and exit
			$this->response([
				'message' => sprintf($this->lang->line("notfound_label"), "Rental ID")
			], REST_Controller::HTTP_NOT_FOUND);
			return;
		}
		
		if (!is_array($data) || count($data) == 0){
			$this->set_response([
                'message' => sprintf($this->lang->line("required_label"), "Data Rental")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		
		$error_message = array();
        if (empty($data["car-id"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[car-id]");
		} else {
			$car = $this->m_cars->get($data["car-id"]);
			if(!$car){
				$this->set_response([
					'message' => sprintf($this->lang->line("notfound_label"), "Car ID")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
		}
		
        if (empty($data["client-id"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[client-id]");
		} else {
			$client = $this->m_clients->get($data["client-id"]);
			if(!$client){
				$this->set_response([
					'message' => sprintf($this->lang->line("notfound_label"), "Client ID")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
		}
		
        if (empty($data["date-from"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[date-from]");
		}
		
        if (empty($data["date-to"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[date-to]");
		}
		
		$is_updated = 0;
		if(count($error_message) > 0){
			// Set the response and exit
            $this->response([
                'message' => (count($error_message) == 1 ? $error_message[0] : implode(", ", $error_message))
            ], REST_Controller::HTTP_NOT_FOUND);
			return;
		} else {
			$date_from = date("Y-m-d", strtotime($data["date-from"]));
			$date_to = date("Y-m-d", strtotime($data["date-to"]));
			$rent_date = array(
				"from" => date("Y-m-d", strtotime("+1 day")),
				"to" => date("Y-m-d", strtotime("+7 day")),
			);
			
			$valid_rent_date = (
				($date_from >= $rent_date["from"] && $date_from <= $rent_date["to"])
				&&
				($date_to >= $rent_date["from"] && $date_to <= $rent_date["to"])
				? true : false
			);
			
			if($date_from > $date_to || !$valid_rent_date){
				$this->set_response([
					'message' => $this->lang->line("invalid_range_date_label")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
			
			$day_diff = $this->day_diff($date_from, $date_to);			
			if($day_diff > $this->get_max_rent_duration()){
				$this->set_response([
					'message' => $this->lang->line("rent_max_duration_label")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
			
			$client_id = (int) $data["client-id"];
			$is_client_rent_car = $this->is_client_rent_car($client_id, $date_from, $date_to, $rental_id);
			if($is_client_rent_car){
				$this->set_response([
					'message' => $this->lang->line("client_rent_car_label")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
			
			$car_id = (int) $data["car-id"];
			$is_car_rented = $this->is_car_rented($car_id, $date_from, $date_to, $rental_id);
			if($is_car_rented){
				$this->set_response([
					'message' => $this->lang->line("car_rented_label")
				], REST_Controller::HTTP_BAD_REQUEST);
				return;
			}
			
			$data_input = array(
				"car_id" => $car_id,
				"client_id" => $client_id,
				"date_from" => $date_from,
				"date_to" => $date_to,
				"updated_at" => date("Y-m-d H:i:s"),
			);
			$is_updated = $this->m_rentals->update($rental_id, $data_input);
			
		}
		
        if ($is_updated){
            $this->set_response(NULL,REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("edit_failed"), "Rental")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }	
	
	/**
	* delete rental
	* input: 
	*	id
	* ounput:
	*/
    public function index_delete($rental_id = 0)
    {
		$rental_id = (int) $rental_id;
		
		$this->load->model("m_rentals");
		
		$is_deleted = 0;
		$car_data = $this->m_rentals->get($rental_id);
		if($car_data){			
			$is_deleted = $this->m_rentals->delete($rental_id);
		} else {
			$this->response([
				'message' => sprintf($this->lang->line("notfound_label"), "Rental ID")
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
	* get all rentals
	* input: 
	* ounput:
	* 	array of rentals
	*/
    public function index_get()
    {		
		$this->load->model("m_rentals");
		$rentals = $this->m_rentals->get_all_custom();
		
        if (is_array($rentals)){
            $this->set_response($rentals,REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("notfound_label"), "Rentals")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Histories extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
    }
	
	/*
	* regex check month format, must be MM-YYYY
	*/
	private function check_month_format($month){
		if (preg_match("/^(0[1-9]|1[0-2])-[0-9]{4}$/", $month)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	* get client rental histories
	* input: 
	*	$client_id
	* ounput:
	* 	array of rentals
	*/
    public function client_get($client_id = 0)
    {		
		$client_id = (int) $client_id;
		$this->load->model("m_clients");
        
		$client_data = $this->m_clients->get($client_id);
		if(!$client_data){
			// Set the response and exit
			$this->response([
				'message' => sprintf($this->lang->line("notfound_label"), "Client ID")
			], REST_Controller::HTTP_NOT_FOUND);
			return;
		}
		
		$this->load->model("m_rentals");
		$client_data->histories = $this->m_rentals->get_histories_client($client_id);
		
        if ($client_data){
            $this->set_response($client_data,REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("notfound_label"), "Rentals Histories")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
	
	/**
	* get car rental histories
	* input: 
	*	$car_id
	* ounput:
	* 	array of rentals
	*/
    public function car_get($car_id = 0)
    {		
		$car_id = (int) $car_id;
		$month = $this->get("month");
		$this->load->model("m_cars");
        
		$car_data = $this->m_cars->get($car_id);
		if(!$car_data){
			// Set the response and exit
			$this->response([
				'message' => sprintf($this->lang->line("notfound_label"), "Car ID")
			], REST_Controller::HTTP_NOT_FOUND);
			return;
		}
		
		if(!$month){
			$this->set_response([
                'message' => sprintf($this->lang->line("required_label"), "Month")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		
		$valid_month_format = $this->check_month_format($month);
		if(!$valid_month_format){
			$this->set_response([
                'message' => $this->lang->line("invalid_month_format_label")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;			
		}
		
		$this->load->model("m_rentals");
		$car_data->histories = $this->m_rentals->get_histories_car($car_id, $month);
		
        if ($car_data){
            $this->set_response($car_data,REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("notfound_label"), "Rentals Histories")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
}

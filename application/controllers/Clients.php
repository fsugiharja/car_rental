<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Clients extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
    }
	
	private function get_gender(){
		return array("male", "female");
	}
	
	/**
	* create new clients
	* input: 
	*	data[name]
	*	data[gender] : must be "male" or "female"
	* ounput:
	*	id
	*/
    public function index_post()
    {
        $data = $this->post('data');
		
        if (!is_array($data) || count($data) == 0){
			$this->set_response([
                'message' => sprintf($this->lang->line("required_label"), "Data Client")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		
		$error_message = array();
        if (empty($data["name"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[name]");
		}
		
        if (empty($data["gender"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[gender]");
		} else {
			$array_gender = $this->get_gender();
			if(!in_array($data["gender"], $array_gender)){
				$error_message[] = $this->lang->line("invalid_gender_label");
			}
		}
		
		$client_id = 0;
		if(count($error_message) > 0){
			// Set the response and exit
            $this->response([
                'message' => (count($error_message) == 1 ? $error_message[0] : implode(", ", $error_message))
            ], REST_Controller::HTTP_NOT_FOUND);
			return;
		} else {
			$this->load->model("m_clients");
			$data_input = array(
				"name" => $data["name"],
				"gender" => $data["gender"],
				"created_at" => date("Y-m-d H:i:s"),
			);
			$client_id = $this->m_clients->insert($data_input);
		}
		
        if ($client_id){
            $this->set_response([
                'id' => $client_id,
            ], REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("create_failed"), "Client")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
	
	/**
	* update clients
	* input: 
	*	data[name]
	*	data[gender] : must be "male" or "female"
	* ounput:
	*/
    public function index_put($client_id = 0)
    {
		$client_id = (int) $client_id;
        $data = $this->put('data');        
        
		$this->load->model("m_clients");
		
		$client_data = $this->m_clients->get($client_id);
		if(!$client_data){
			// Set the response and exit
			$this->response([
				'message' => sprintf($this->lang->line("notfound_label"), "Client ID")
			], REST_Controller::HTTP_NOT_FOUND);
			return;
		}
		
        if (!is_array($data) || count($data) == 0){
			$this->set_response([
                'message' => sprintf($this->lang->line("required_label"), "Data Client")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		
        if (!is_array($data) || count($data) == 0){
			$this->set_response([
                'message' => sprintf($this->lang->line("required_label"), "Data Client")
            ], REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		
		$error_message = array();
        if (empty($data["name"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[name]");
		}
		
        if (empty($data["gender"])){
			$error_message[] = sprintf($this->lang->line("required_label"), "data[gender]");
		} else {
			$array_gender = $this->get_gender();
			if(!in_array($data["gender"], $array_gender)){
				$error_message[] = $this->lang->line("invalid_gender_label");
			}
		}
		
		$is_updated = 0;
		if(count($error_message) > 0){
			// Set the response and exit
            $this->response([
                'message' => (count($error_message) == 1 ? $error_message[0] : implode(", ", $error_message))
            ], REST_Controller::HTTP_NOT_FOUND);
			return;
		} else {
			$data_input = array(
				"name" => $data["name"],
				"gender" => $data["gender"],
				"updated_at" => date("Y-m-d H:i:s"),
			);
			$is_updated = $this->m_clients->update($client_id, $data_input);
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
	* delete client
	* input: 
	*	id
	* ounput:
	*/
    public function index_delete($client_id = 0)
    {
		$client_id = (int) $client_id;
		
		$this->load->model("m_clients");
		
		$is_deleted = 0;
		$client_data = $this->m_clients->get($client_id);
		if($client_data){			
			$is_deleted = $this->m_clients->delete($client_id);
		} else {
			$this->response([
				'message' => sprintf($this->lang->line("notfound_label"), "Client ID")
			], REST_Controller::HTTP_NOT_FOUND);
			return;
		}
		
        if ($is_deleted){
            $this->set_response(NULL,REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("delete_failed"), "Client")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
	
	/**
	* get all clients
	* input: 
	* ounput:
	* 	array of clients
	*/
    public function index_get()
    {
		$this->load->model("m_clients");
		$clients = $this->m_clients->get_all();
		
        if (is_array($clients)){
            $this->set_response($clients,REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'message' => sprintf($this->lang->line("notfound_label"), "Client")
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migrations extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('migration');
	}
	public function index(){
		if(!$this->migration->latest()){
		  show_error($this->migration->error_string());
		}else{
		  echo "database updated to latest version";
		}
	}

	public function upgrade($version){
		if(!$this->migration->version($version)){
		  show_error($this->migration->error_string());
		}else{
		  echo "database updated to version $version";
		}
	}

	public function downgrade($version){
		if(!$this->migration->version($version)){
		  show_error($this->migration->error_string());
		}else{
		  echo "database downgraded to version $version";
		}
	}
}
<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\REST_Controller_Definitions;

$REST_PATH = APPPATH.'libraries\REST_Controller.php';
$REST_DEF_PATH = APPPATH.'libraries\REST_Controller_Definitions.php';
require ($REST_PATH);
require ($REST_DEF_PATH);


class Register extends CI_Controller
{
    use REST_Controller {
       REST_Controller::__construct as private __resTraitConstruct;
    }

	public function index_post() {
		$data['username']	= $this->post('username');
		$data['password']	= md5($this->post('password'));
		$data['fullname']	= $this->post('fullname');
		$data['email']	= $this->post('email');
		$data['created_at'] = date("Y-m-d H:i:s");

		$data_result['status'] = false;

		if (isset($data['username']) && !empty($data['username']) && isset($data['email'])
			&& !empty($data['email']) && isset($data['password']) && !empty($data['password'])
			&& isset($data['fullname']) && !empty($data['fullname'])) {

			if($this->wasUsernameTaken($data['username'])) {
				$data_result['msg'] = "Your username was taken by another user";
			} else if($this->wasEmailTaken($data['email'])) {
				$data_result['msg'] = "Your email was taken by another user";
			} else {
				$insert = $this->db->insert('users', $data);
				if($insert) {
					$data_result['status'] = true;
					$data_result['msg'] = "Registration Successfull";
				} else {
					$data_result['msg'] = "Registration failed, please try again";
				}
			}
		}
		else {
			$data_result['msg'] = "All field should be not empty. Please check again";
		}

		$this->response($data_result, REST_Controller_Definitions::HTTP_OK);
	}

	public function wasUsernameTaken($username) {
		$this->db->select("*");
		$this->db->where("username", $username);
		$this->db->from("users");
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return true;
		}
		return false;
	}

	public function wasEmailTaken($email) {
		$this->db->select("*");
		$this->db->where("email", $email);
		$this->db->from("users");
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return true;
		}
		return false;
	}

}

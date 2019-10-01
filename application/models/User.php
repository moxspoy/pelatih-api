<?php
/**
 * Created by   : MNurilmanBaehaqi
 * Date         : 01 October 2019
 * Time         : 13:40
 * Github       : https://github.com/moxspoy
 */

class User extends CI_Model
{
	public function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->database();
		$this->load->library('form_validation');
	}

	public function register_user($data) {
		$this->form_validation->set_rules('username', 'Username', 'required|is_unique[user.username]');
		$this->form_validation->set_rules('email', 'Email', 'required|is_unique[user.email]');
		if($this->form_validation->run() == FALSE){
			return "E101";
		} else {
			$data_u = array(
				'username'  => $data['username'],
				'email' => $data['email'],
				'password' => md5($data['password']),
				'fullname' => $data['fullname'],
				'created_at' => date("Y-m-d H:i:s"));

			$inserted = $this->db->insert('user', $data_u);

			if($inserted){
				return "S";
			}else{
				return "E103";
			}
		}
	}

	public function login_user($data){
		$username = $data['username'];
		$password = md5($data['password']);

		$this->db->select("*");
		$this->db->where("username", $username);
		$this->db->where("password", $password);
		$this->db->from("user");
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$this->db->where("username", $username);
			$this->db->update('user', array('auth_key' => Token::generateToken()));
			$data = $this->getUser($query->result_array()[0]['id']);
			return $data;
		}else{
			return "E101";
		}
	}

}

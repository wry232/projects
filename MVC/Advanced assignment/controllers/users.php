<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("main.php");

class Users extends Main {
	
	public $view_data = array();
		
	public function __construct()
	{
		parent:: __construct();
	}
	
	public function dashboard()
	{
		$this->load->model("test_model");
		$user_info = $this->test_model->get_all_users();
		
		foreach($user_info->result() as $key)
		{
			$id[] = $key->id;
			$name[] = $key->first_name . " " . $key->last_name;
			$email[] = $key->email;
			$created_at[] = $key->created_at;
			$user_level[] = $key->user_level;
		}
		
		$view_data["user_data"] = array("id" => $id,
										"name" => $name,
										"email" => $email,
										"created_at" => $created_at,
										"user_level" => $user_level
										);
		
		$view_data["user_id"] = $this->user_session->id;
		$this->load->view("user_dashboard", $view_data);
	}
	
	public function show($user_id)
	{
		$this->load->model("test_model");
		$user_info = $this->test_model->get_user(NULL, $user_id);
		$messages = $this->test_model->get_messages($user_id);
		
		$view_data["user_data"] = array("id" => $user_info->id,
										"first_name" => $user_info->first_name,
										"last_name" => $user_info->last_name,
										"created_at" => $user_info->created_at,
										"id" => $user_info->id,
										"email" => $user_info->email,
										"description" => $user_info->description
										);
		
		foreach($messages->result() as $key)
		{
			$posted_by[] = $key->first_name . " " . $key->last_name;
			$messages = $key->message;
			$created_at[] = $key->created_at;
		}
		
		$view_data["messages_info"] = array("posted_by" => $posted_by,
									  "message" => $messages,
									  "posted_at" => $created_at
									  );
		
		$this->load->view("user_info", $view_data);
	}
	
	public function edit()
	{
		$this->load->model("test_model");
		$user_info = $this->test_model->get_user(NULL, $this->user_session->id);
		
		$view_data["user_data"] = array("id" => $user_info->id,
										"first_name" => $user_info->first_name,
										"last_name" => $user_info->last_name,
										"email" => $user_info->email,
										"description" => $user_info->description
										);
		
		$this->load->view("edit_profile", $view_data);
	}
	
	public function process_edit_profile($user_id)
	{
		$this->load->library("form_validation");
		$this->form_validation->set_rules("first_name", "First Name", "trim|required");
		$this->form_validation->set_rules("last_name", "Last Name", "trim|required");
		$this->form_validation->set_rules("email", "Email", "trim|valid_email|required");
		
		if($this->form_validation->run() === FALSE)
		{
			$this->load->model("test_model");
			$user_info = $this->test_model->get_user(NULL, $user_id);
			
			$view_data["user_data"] = array("id" => $user_info->id,
											"first_name" => $user_info->first_name,
											"last_name" => $user_info->last_name,
											"email" => $user_info->email,
											"description" => $user_info->description
											);
			
			$view_data["info_errors"] = validation_errors();
			$this->load->view("edit_profile", $view_data);
		}
		else
		{
			$user = $this->input->post();
			$user_info = array("id" => $user_id,
							   "first_name" => $user["first_name"],
							   "last_name" => $user["last_name"],
							   "email" => $user["email"]
							   );
							   
			$this->load->model("test_model");
			$new_user_info = $this->test_model->edit_user($user_info);
			
			$view_data["user_data"] = array("id" => $new_user_info->id,
											"first_name" => $new_user_info->first_name,
											"last_name" => $new_user_info->last_name,
											"email" => $new_user_info->email,
											"description" => $new_user_info->description
											);
			
			$view_data["info_success"] = "Your information was successfully changed!";
			$this->load->view("edit_profile", $view_data);
			echo "Your information was successfully changed!";
		}
	}
	
	public function process_change_password($user_id)
	{
		$this->load->library("form_validation");
		$this->form_validation->set_rules("password", "Password", "trim|min_length[8]|required|matches[confirm_password]|md5");
		$this->form_validation->set_rules("confirm_password", "Confirm Password", "trim|required|md5");
		
		if($this->form_validation->run() === FALSE)
		{
			$this->load->model("test_model");
			$user_info = $this->test_model->get_user(NULL, $user_id);
			
			$view_data["user_data"] = array("id" => $user_info->id,
											"first_name" => $user_info->first_name,
											"last_name" => $user_info->last_name,
											"email" => $user_info->email,
											"description" => $user_info->description
											);
			
			$view_data["password_errors"] = validation_errors();
			$this->load->view("edit_profile", $view_data);
		}
		else
		{
			$user = $this->input->post();
			$user_info = array("id" => $user_id,
							   "first_name" => $user["first_name"],
							   "last_name" => $user["last_name"],
							   "email" => $user["email"]
							   );
							   
			$this->load->model("test_model");
			$new_user_info = $this->test_model->edit_user($user_info);
			
			$view_data["user_data"] = array("id" => $new_user_info->id,
											"first_name" => $new_user_info->first_name,
											"last_name" => $new_user_info->last_name,
											"email" => $new_user_info->email,
											"description" => $new_user_info->description
											);
			
			$view_data["password_success"] = "Your password was successfully changed!";
			$this->load->view("edit_profile", $view_data);
			echo "Your information was successfully changed!";
		}
	}
	
	public function process_edit_description($user_id)
	{
		$user = $this->input->post();
		$user_info = array("id" => $user_id, "description" => $user["description"]);
						   
		$this->load->model("test_model");
		$new_user_info = $this->test_model->edit_user($user_info);
		
		$view_data["user_data"] = array("id" => $new_user_info->id,
										"first_name" => $new_user_info->first_name,
										"last_name" => $new_user_info->last_name,
										"email" => $new_user_info->email,
										"description" => $new_user_info->description
										);
		
		$view_data["description_success"] = "Your profile description was successfully changed!";
		$this->load->view("edit_profile", $view_data);
		echo "Your information was successfully changed!";
	}
}
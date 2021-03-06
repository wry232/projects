<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notes extends CI_Controller {
	
	public $view_data;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->view("note_app");
	}
	
	public function get_notes()
	{
		$this->load->model("note_model");
		$notes = $this->note_model->get_all_notes();
		
		if($notes)
		{
			foreach($notes->result() as $note)
			{
				$notes_data[] = "<div class='note_container'><div class='title'>{$note->title}</div>
								 <div class='note' id='{$note->id}'>{$note->description}</div>
								 <p class='delete' id='{$note->id}'>Click here to delete {$note->title}</p></div>";
			}
			
			$data["notes"] = $notes_data;
			$data["status"] = true;
		}
		else
		{
			$data["error"] = "<p>No notes posted.</p>";
			$data["status"] = false;
		}
		
		echo json_encode($data);
	}
	
	public function post_note()
	{
		$post = $this->input->post();
		$post["created_at"] = date("Y-m-d h:i:s");
		$this->load->model("note_model");
		$post_note = $this->note_model->insert_note($post);
		
		if($post_note)
		{
			$data["new_note"] = "<div class='note_container'><div class='title'>{$post['title']}</div>
								 <div class='note' id='{$this->db->insert_id()}'>{$post['description']}</div>
								 <p class='delete' id='{$this->db->insert_id()}'>Click here to delete {$post['title']}</p></div>";
			$data["message"] = "<p>Note successfully added.</p>";
			$data["status"] = true;
		}
		else
		{
			$data["error"] = "<p>Sorry, but your note cannot be posted.</p>";
			$data["status"] = false;
		}	
		
		echo json_encode($data);
	}
	
	public function edit($id)
	{
		$note_info = $this->input->get();
		$note_info["id"] = $id;
		$note_info["updated_at"] = date("Y-m-d H:i:s");
		$this->load->model("note_model");
		$updated_note = $this->note_model->edit_note($note_info);
		
		if($updated_note)
			$data["message"] = "Note has been updated.";
		else
			$data["message"] = "Note cannot be updated.";
		
		echo json_encode($data);
	}
	
	public function delete($note_id)
	{
		$this->load->model("note_model");
		$deleted_note = $this->note_model->delete_note($note_id);
		
		if($deleted_note)
			$data["message"] = "Note has been deleted.";
		else
			$data["message"] = "Note cannot be deleted.";
		
		echo json_encode($data);
	}
}
//eof
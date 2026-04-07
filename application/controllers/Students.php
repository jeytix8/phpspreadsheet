<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Students extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Student_model');    // load student_model class, parang require_once
    }

    public function index()
    {
        $data['students'] = $this->Student_model->get_all();

        $this->load->view('students/index', $data);
    }

    public function create()
    {  // url: http://localhost/ci3/index.php/students/create
        if ($this->input->post()) {  //  may post request ba?
            $data = [
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'course' => $this->input->post('course'),
            ];

            $this->Student_model->insert($data);
            $this->session->set_flashdata('message', 'Student added successfully!');
            redirect('students');
        }
        $this->load->view('students/create');   // Niload yung views (frontend) na ang path ay students/create.php
    }

    public function edit($id)
    {
        $student = $this->Student_model->get_by_id($id);

        if (!$student) {
            show_404();
        }

        if ($this->input->post()){
            $data = [
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'course' => $this->input->post('course')
            ];

            $this->Student_model->update($id, $data);
            $this->session->set_flashdata('message', 'Student updated successfully!');
            redirect('students');
        }

        $data['student'] = $student;
        $this->load->view('students/edit', $data);
    }

    public function delete($id){
        $this->Student_model->delete($id);
        $this->session->set_flashdata('message', 'Student deleted successfully!');
        redirect('students');
    }
}

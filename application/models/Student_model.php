<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_model extends CI_Model{
    public function insert($data){
        return $this->db->insert('students', $data);
    }

    public function get_all(){
        return $this->db->get('students')->result();
    }
    
    public function get_by_id($id){
        return $this->db->get_where('students', ['id' => $id])->row();
    }
    
    public function update($id, $data){
        $this->db->where('id', $id);
        return $this->db->update('students', $data);
    }

    public function delete($id){
        $this->db->where('id', $id);
        return $this->db->delete('students');
    }
}
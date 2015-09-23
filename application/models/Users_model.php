<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Users_model extends CI_Model {

    public function __construct() {
        // Call the CI_Model constructor
        parent::__construct();
    }
    
    
    public function get_user_id_by_email($email) {
        log_message('info', '----------- user email: ' . $email);
        $query = $this->db->get_where('users', array('email' => $email));
        if ($query->num_rows() != 1) {
            return FALSE;
        } else {
            $user_id = $query->result()[0]->id;
            log_message('info', 'User found. ID:'.$user_id);
            return $user_id;
        }
    }
    
    
    public function addNewUser($param) {
        $this->db->insert('users',$param);
        $insertedId = $this->db->insert_id();
        if (!$insertedId){
            return FALSE;
        } else {
            return $insertedId;
        }
    }
    
    public function get_user_pass_by_email($email) {
        log_message('info', '----------- user email: ' . $email);
        $query = $this->db->get_where('users', array('email' => $email));
        if ($query->num_rows() != 1) {
            return FALSE;
        } else {
            $user_pass = $query->result()[0]->password;
            return $user_pass;
        }
    }
    
    

}

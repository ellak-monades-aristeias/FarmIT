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
            log_message('info', 'User found. ID:' . $user_id);
            return $user_id;
        }
    }

    public function addNewUser($param) {
        $this->db->insert('users', $param);
        $insertedId = $this->db->insert_id();
        if (!$insertedId) {
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

    public function update_user($updateFields, $userId) {
        try {
            $this->db->where('id', $userId);
            $this->db->update('users', $updateFields);
            if ($this->db->affected_rows() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            log_message('info', $ex->getMessage());
            return FALSE;
        }
    }

    public function add_new_company($arData, $user_id) {
        try {
            $query = $this->db->get_where('companies', array('user_id' => $user_id));
            if ($query->num_rows() > 0) {
                return FALSE;
            } else {
                $this->db->insert('companies', $arData);
                if ($this->db->affected_rows() == 1) {
                    $this->db->where('users.id', $user_id);
                    $this->db->update('users', array('is_company' => 1));
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        } catch (Exception $ex) {
            log_message('info', $ex->getMessage() . 'code ' . $ex->getCode());
            if ($ex->getCode() == 23000) { //if SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

}

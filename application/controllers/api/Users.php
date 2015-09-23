<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * @package    CodeIgniter
 * @subpackage    Rest Server
 * @category    Controller
 * @author      Christos Sardianos
 * @date    21-09-2015
 */
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Users extends REST_Controller {

    function __construct() {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE");
        // Construct the parent class
        parent::__construct();
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        //$this->methods['user_get']['limit'] = 500; // 500 requests per hour per user/key
        //$this->methods['user_post']['limit'] = 100; // 100 requests per hour per user/key
        //$this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    function register_post() {
        if ((!$this->post('email') || !$this->post('password') || !$this->post('name') || !$this->post('surname'))) {
            $this->response([
                'status' => FALSE,
                'message' => 'You have not provided complete registration data.'
                    ], REST_Controller::HTTP_BAD_REQUEST);
//            $message = array('success' => 'false');
//            $this->response($message, 400);
        }

        $email = $this->post('email');
        // Remove all illegal characters from email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            log_message('info', $email . " is a valid email address");
        } else {
            log_message('info', $email . ' is not a valid email address');
            $this->response([
                'status' => FALSE,
                'message' => 'Email address is not valid'
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $user_id = $this->users_model->get_user_id_by_email($email);
        if ($user_id) {
            $this->response([
                'status' => TRUE,
                'message' => 'User already registered with userid=' . $user_id
                    ], REST_Controller::HTTP_OK);
        } else {
            $data = array();
            $data['email'] = $email;
            $data['password'] = md5($this->post('password'));
            $data['name'] = $this->post('name');
            $data['surname'] = $this->post('surname');
            $addedUserId = $this->users_model->addNewUser($data);
            if (!$addedUserId) {
                $this->response([
                    'status' => FALSE,
                    'message' => 'User not inserted.'
                        ], REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $this->response([
                    'status' => TRUE,
                    'message' => 'User registered with userid=' . $addedUserId
                        ], REST_Controller::HTTP_OK);
            }
        }
    }

    function login_post() {
        if ((!$this->post('email') || !$this->post('password'))) {
            $$this->response([
                'status' => FALSE,
                'message' => 'You have not provided complete login data.'
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
        $email = $this->post('email');
        $pass = $this->post('password');
        // Remove all illegal characters from email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            log_message('info', "$email is a valid email address");
        } else {
            log_message('info', "$email is not a valid email address");
            $this->response([
                'status' => FALSE,
                'message' => 'User not inserted.'
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
        $db_pass = $this->users_model->get_user_pass_by_email($email);
        if (!$db_pass) {
            $this->response([
                'status' => FALSE,
                'message' => 'User not found.'
                    ], REST_Controller::HTTP_FORBIDDEN);
        }
        if (md5($pass) == $db_pass) {
            $this->response([
                'status' => TRUE,
                'message' => 'User authenticated.'
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'User not authorozied.'
                    ], REST_Controller::HTTP_FORBIDDEN);
        }
    }

    function updateinfo_post() {
        $var_email = $this->post('email');
        $var_name = $this->post('name');
        $var_surname = $this->post('surname');
        $var_tel = $this->post('tel_num');
        
        $arData = array();
        
        if ($var_name) {
            $arData["name"] = $var_name;
        }
        if ($var_surname) {
            $arData["surname"] = $var_surname;
        }
        if ($var_tel) {
            $arData["tel_num"] = $var_tel;
        }
        
        if (!$var_email) {
            $this->response([
                    'status' => FALSE,
                    'message' => 'No valid data provided'
                        ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $user_id = $this->users_model->get_user_id_by_email($var_email);
            if (!$user_id) {
                $this->response([
                    'status' => FALSE,
                    'message' => 'No valid data provided'
                        ], REST_Controller::HTTP_BAD_REQUEST);
            }

            if ($this->users_model->update_user($arData, $user_id)) {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Data updated successfully.'
                        ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Data not updated.'
                        ], REST_Controller::HTTP_OK);
            }
        }
    }

}

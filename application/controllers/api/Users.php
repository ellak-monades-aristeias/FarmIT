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
        if ((!$this->post('email') || !$this->post('password') || !$this->post('name') || !$this->post('surname') || !$this->post('tel_num'))) {
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
            $data['tel_num'] = $this->post('tel_num');
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
        $email = $this->post('email');
        $name = $this->post('name');
        $surname = $this->post('surname');
        $tel = $this->post('tel_num');

        $arData = array();

        if ($name) {
            $arData["name"] = $var_name;
        }
        if ($surname) {
            $arData["surname"] = $var_surname;
        }
        if ($tel) {
            $arData["tel_num"] = $var_tel;
        }

        if (!$email) {
            $this->response([
                'status' => FALSE,
                'message' => 'No valid data provided'
                    ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $user_id = $this->users_model->get_user_id_by_email($email);
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

    function becomeproducer_post() {
        $email = $this->post('email');
        $firm = $this->post('firm');
        $firm_desc = $this->post('firm_desc');
        $afm = $this->post('afm');
        $doy = $this->post('doy');
        $occupation = $this->post('occupation');
        $address = $this->post('address');
        $address_no = $this->post('address_no');
        $address_area = $this->post('address_area');
        $address_zip_code = $this->post('address_zip_code');
        $tel1 = $this->post('tel1');
        $tel2 = $this->post('tel2');
        $is_producer = $this->post('is_producer');
        $avatar = $this->post('avatar');

        if ((!$email || !$firm || !$firm_desc || !$afm || !$doy || !$occupation || !$address || !$address_no || !$address_area || !$address_zip_code || !$tel1 || !$is_producer)) {
            $this->response([
                'status' => FALSE,
                'message' => 'You have not provided complete data.'
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $user_id = $this->users_model->get_user_id_by_email($email);
        $arData = array(
            "user_id" => $user_id,
            "firm" => $firm,
            "firm_desc" => $firm_desc,
            "afm" => $afm,
            "doy" => $doy,
            "occupation" => $occupation,
            "address" => $address,
            "address_no" => $address_no,
            "address_area" => $address_area,
            "address_zip_code" => $address_zip_code,
            "tel1" => $tel1,
            "is_producer" => $is_producer,
        );
        if ($avatar) {
            $arData["avatar"] = $avatar;
        }
        if ($tel2) {
            $arData["tel2"] = $tel2;
        }

        $result = $this->users_model->add_new_company($arData, $user_id);
        if ($result) {
            $this->response([
                'status' => TRUE,
                'message' => 'New company added'
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Something wrong happened'
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    function addproduct_post() {
        $email = $this->post('email');
        $name = $this->post('pr_name');
        $desc = $this->post('pr_description');
        $price = $this->post('pr_price');
        $unit = $this->post('pr_unit'); //(eg. unit, kilos etc)
        $category = $this->post('category'); //(eg. unit, kilos etc)
        $stock = $this->post('stock'); //(eg. unit, kilos etc)

        $user_id = $this->users_model->get_user_id_by_email($email);
        if (!$user_id || $name || $desc || $price || $unit || $category || $stock) {
            $this->response([
                'status' => FALSE,
                'message' => 'No valid data provided'
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = array(
            "producer_id" => $user_id,
            "name" => $name,
            "desc" => $desc,
            "unit_price" => $price,
            "unit" => $unit,
            "category_id" => $category,
            "stock" => $stock,
        );

        $resp = $this->users_model->add_product($data, $user_id);
        if ($resp) {
            $this->response([
                'status' => TRUE,
                'message' => 'Product was added'
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Product was not added'
                    ], REST_Controller::HTTP_OK);
        }
    }
    
    function updateproduct_post() {
        $email = $this->post('email');
        $name = $this->post('pr_name');
        $price = $this->post('pr_price');
        $unit = $this->post('pr_unit');
        $stock = $this->post('stock'); //(eg. unit, kilos etc)

        $user_id = $this->users_model->get_user_id_by_email($email);
        if (!$user_id || !$name || (!$price && !$unit && !$stock)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No valid data provided'
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
        
        $data = array();
        $data["producer_id"] = $user_id;
        $data["name"] = $name;
        
        if ($price) {
            $data["unit_price"] = $price;
        }
        if ($unit) {
            $data["unit"] = $unit;
        }
        if ($stock) {
            $data["stock"] = $stock;
        }
        
        $resp = $this->users_model->update_product($user_id, $data, $name);
        if ($resp) {
            $this->response([
                'status' => TRUE,
                'message' => 'Product info updated'
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Product info not updated'
                    ], REST_Controller::HTTP_OK);
        }
    }
    
    
    function removeproduct_post() {
        $email = $this->post('email');
        $name = $this->post('pr_name');

        $user_id = $this->users_model->get_user_id_by_email($email);
        if (!$user_id || !$name) {
            $this->response([
                'status' => FALSE,
                'message' => 'No valid data provided'
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
        
        $data = array();
        $data["producer_id"] = $user_id;
        $data["name"] = $name;
        
        $resp = $this->users_model->remove_product($user_id, $name);
        if ($resp) {
            $this->response([
                'status' => TRUE,
                'message' => 'Product was removed'
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Product was not removed'
                    ], REST_Controller::HTTP_OK);
        }
    }
    

}

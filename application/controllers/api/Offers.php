<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * @package    CodeIgniter
 * @subpackage    Rest Server
 * @category    Controller
 * @author      Christos Sardianos
 * @date    11-11-2015
 */

require APPPATH . '/libraries/REST_Controller.php';

class Offers extends REST_Controller {

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
    
}
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        $this->load->view('welcome_message');
    }

    public function test() {
        //echo myocr();
        $afm = '111111111';//094458113
        $returned_data = get_from_gsis_ours($afm);
        $returned_afm = $returned_data['envBody']['mrgWsPublicAfmMethodResponse']['RgWsPublicBasicRt_out']['mafm'];
        var_dump($returned_afm);
        if (!$returned_afm) {
            echo 'not';
        } else {
            echo 'yes';
        }
    }

    public function check_afm() {
        try {

            $opts = array(
                'http' => array(
                    'user_agent' => 'PHPSoapClient'
                )
            );

            $context = stream_context_create($opts);
            $client = new SoapClient('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl', array('stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE));

            $result = $client->checkVat(array(
                'countryCode' => 'DK',
                'vatNumber' => '47458714'
            ));
            print_r($result);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        //$url='https://ec.europa.eu/taxation_customs/tin/checkTinService.wsdl';
        ///$client = new SoapClient($url);
        //var_dump($client->checkTin('EL','144324892'));
    }
    
    public function index2() {
        $this->load->view('welcome_message');
    }
    

}

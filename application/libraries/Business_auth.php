<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Business_auth
{
    protected $CI;
    public $error_desc;

    function __construct()
    {
        $this->CI = &get_instance();
    }

    public function is_logged_in()
    {
        if ($this->CI->session->userdata('member_id')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function login($email, $password)
    {
        //do the hash
        $password = md5($password);
        $member = $this->CI->member_model->get_by_email_and_password($email, $password);
        if ($member)
        {
            $this->save_into_session($member);
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function logout()
    {
        $this->CI->session->unset_userdata('member_id');
        $this->CI->session->unset_userdata('member_email');
        $this->CI->session->unset_userdata('member_name');

        if($this->is_business_member())
        {
            $this->CI->session->unset_userdata('busmem_id');
        }

        $this->CI->session->sess_destroy();
    }

    public function register($name, $surname, $email, $password)
    {
        $member_api_key = "";
        $member_api_key = $this->generate_member_api_key($email, $password);
        $activation_code = $this->_random_key(20);
        $registration_date=  convert_date_to_str();

        $password = md5($password);


        $member_id = $this->CI->member_model->register($name, $surname, $email, $password, $registration_date, $activation_code, $member_api_key);

        if($member_id)
        {
            $registered_member = $this->CI->member_model->get_by_id($member_id);

            if($registered_member)
            {
                return $registered_member;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }

    }

    public function confirm($activation_code)
    {
        $member = $this->CI->member_model->get_by_activation_code($activation_code);

        if ($member)
        {
            $this->CI->member_model->activate($member->member_id,  convert_date_to_str());

            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function save_into_session($member)
    {
        $data = array(
            'bus_afm' => $member->member_email,
            'name' => $member->member_name,
        );


        $this->CI->session->set_userdata($data);
    }

}
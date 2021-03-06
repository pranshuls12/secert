<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
    {
        parent:: __construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('level_model');
		$this->load->library('email');
		$this->load->helper('cookie');
    }

	public function home()
	{
		$this->load->view('home-view');
	}

	public function user_login_get()
	{
		// $this->logout();
		
        // $email = $this->session->userdata('mail');
        // if (isset($email)) {
        //     redirect(base_url('home'));
        // }

        $this->form_validation->set_rules('user_email', 'Email', 'trim');
        $this->form_validation->set_rules('user_password', 'Password', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('login-view');
        } else {
            $email = $this->input->post('user_email');
			$password = $this->input->post('user_password');

			$login_data = $this->level_model->user_login($email, $password);
			
            if ($login_data) {
				$session_data = array(
                    'id' => $login_data->id,
                    'mail' => $login_data->email,
                    'level'=> $login_data->user_level,
                    // 'time'=> $login_data->login_time
				);

				// $cookie = array(
				// 	'suspect'   => '0'
				// 	);
				
				// set_cookie($cookie);
				// $this->input->set_cookie('suspect','0');

                $this->session->set_userdata($session_data);
                redirect(base_url('level'));
            } else {
                $data = array(
                    'message' => 'Wrong email or password.'
                );
                $this->load->view('login-view', $data);
            }
        }
	}

	public function logout()
    {
        // $time = date('h:i:s',time());
		// $mail = $this->session->userdata('mail');
		// $data= $this->level_model->logout_time($mail,$time);
        $array_items = array('id', 'mail', 'level');
        $this->session->unset_userdata($array_items);
        $this->session->sess_destroy();
        redirect(base_url('login'));
    }

	public function level()
	{
		$id = $this->session->userdata('id');
        if (!isset($id)) {
            redirect(base_url('login'));
		}
		
		$level = $this->session->userdata('level')+1;
		$id = $this->session->userdata('id');
		
		$flag = $this->input->post('flag');
		$level1 =$this->session->userdata('level');
		// echo "<script>alert('$level');</script>";
		
		switch ($level) {
			case '1': //source be with you
			$this->form_validation->set_rules('flag', 'Answer', 'required');
			if ($this->form_validation->run() == FALSE) {
				$data['text'] = '<b>Level 1</b><br><br> I would change the world if GOD provided me the Source code of it !<br><br>';
				$data['level'] = $level;
				$this->load->view('levels', $data);
			} else {
				if($flag == 'takemetonextlevel'){
					$update_data = $this->level_model->update_status($id, $level);
					$this->session->set_userdata('level', $level);
					redirect(base_url('level'));
				}
				else{
					$data['text'] = '<b>Level 1</b><br><br> I would change the world if GOD provided me the Source code of it !<br><br> <p style="color:yellow;">Errrhh! Try Again!</p>';
					$data['level'] = $level;
					$this->load->view('levels', $data);
				}
			}
			
			break;
			
			case '2': //forgot to upload passwd script (empty password field)
				$this->form_validation->set_rules('flag', 'Answer', 'alpha_numeric_spaces');
				if ($this->form_validation->run() == FALSE) {
					$data['text'] = '<b>Level 2</b><br><br>Our new blooming Network Security Ananlyst set up a password protection script. He made it load the real password from an unencrypted text file and compare it to the password the user enters. However, he <span style="color:yellow;">forgot</span> to upload the password file... !<br>Can you guess the string to get past this login form<br><br>';
					$data['level'] = $level;
					$data['encrypted'] = '';
					$this->load->view('levels', $data);
				} else {
					
					if($flag == 'forgot'){
						
						$update_data = $this->level_model->update_status($id, $level);
						$this->session->set_userdata('level', $level);
						redirect(base_url('level'));
					}
					else{
						$data['text'] = '<b>Level 2</b><br><br>Our new blooming Network Security Ananlyst set up a password protection script. He made it load the real password from an unencrypted text file and compare it to the password the user enters. However, he <span style="color:yellow;">forgot</span> to upload the password file... !<br>Can you guess the string to get past this login form<br><br><br><br> <p style="color:yellow;">Errrhh! Try Again!</p>';
						$data['level'] = $level;
						$data['encrypted'] = '';
						$this->load->view('levels', $data);
					}
				}

				break;

			case '3': //cookie manipulation
				$this->input->set_cookie('suspect','0');
				$c = get_cookie('suspect');
				if(get_cookie('suspect')=='1'){
					// $this->input->delete_cookie('suspect');
					delete_cookie('suspect');
					$update_data = $this->level_model->update_status($id, $level);
					$this->session->set_userdata('level', $level);
					redirect(base_url('level'));
				}
				else{
					$data['text'] = '<b>Level 3</b><br><br><br><IMG SRC='.base_url('images/cookie_love.gif').'><br><br><br>';
					$data['level'] = $level;
					$this->load->view('levels', $data);
				}

				break;

			case '4': //QR Code + morse code
				$this->form_validation->set_rules('flag', 'Answer', 'required');
				if ($this->form_validation->run() == FALSE) {
					$data['text'] = '<b>Level 4</b><br><br><br><IMG SRC='.base_url('images/level_4.png').'><br><br><br>';
					$data['level'] = $level;
					$this->load->view('levels', $data);
				} else {
					if($flag == 'CATCHTHEFLAG'){
						$update_data = $this->level_model->update_status($id, $level);
						$this->session->set_userdata('level', $level);
						redirect(base_url('level'));
					}
					else{
						$data['text'] = '<b>Level 4</b><br><br><br><IMG SRC='.base_url('images/level_4.png').'><br><br><br><br><br><br><br> <p style="color:yellow;">Errrhh! Try Again!</p>';
						$data['level'] = $level;
						$this->load->view('levels', $data);
					}
				}				

				break;



			case '5': //deeper source
				$this->form_validation->set_rules('flag', 'Answer', 'required');
				if ($this->form_validation->run() == FALSE) {
					$data['text'] = "<b>Level 5</b><br><br> I changed the world. Now i want to go Deeper and Style up hell too!! Don't worry GOD's with us ;)";
					$data['level'] = $level;
					$this->load->view('levels', $data);
				} else {
					if($flag == 'thisaintacolor'){
						$update_data = $this->level_model->update_status($id, $level);
						$this->session->set_userdata('level', $level);
						redirect(base_url('level'));
					}
					else{
						$data['text'] = "<b>Level 5</b><br><br> I changed the world. Now i want to go Deeper and Style up hell too!! Don't worry GOD's with us ;)<p style='color:yellow;'>Errrhh! Try Again!</p>";
						$data['level'] = $level;
						$this->load->view('levels', $data);
					}
				}
				
				
				break;

			case '6': //t9 cipher
				$this->form_validation->set_rules('flag', 'Answer', 'required');
				if ($this->form_validation->run() == FALSE) {
					$data['text'] = "<b>Level 6</b><br><br>Missing my Nokia 1100<br><br><b style='font-family: sans-serif;'>6444877770222260777788833366802224427833777</b><br><br>";
					$data['level'] = $level;
					$this->load->view('levels', $data);
				} else {
					if($flag == 'mits acm student chapter'){
						$update_data = $this->level_model->update_status($id, $level);
						$this->session->set_userdata('level', $level);
						redirect(base_url('level'));
					}
					else{
						$data['text'] = "<b>Level 6</b><br><br>Missing my Nokia 1100<br><br><b style='font-family: sans-serif;'>6444877770222260777788833366802224427833777</b><br><br><p style='color:yellow;'>Errrhh! Try Again!</p>";
						$data['level'] = $level;
						$this->load->view('levels', $data);
					}
				}
				
				break;


			case '7': //html+directory
				$this->form_validation->set_rules('flag', 'Answer', 'required');
				if ($this->form_validation->run() == FALSE) {
					$data['text'] = "<b>Level 7</b><br><br>Our agents (hackers) informed us that there reasonable suspicion that  the site of this <a href=".base_url('')."level_9/ style='color:red;' target='_blank'>Logistics Company</a> is a blind for a human organs'  smuggling organisation.<br> This organisation attracts its victims through advertisments for jobs  with very high salaries. They choose those ones who do not have many  relatives, they assasinate them and then sell their organs to very rich  clients, at very high prices.<br> These employees are registered in the secret files of the company as 'special clients'!<br> One of our agents has been hired as by the particular company. Unfortunately, since 01/01/2017 <span style='color:red;'> Jason </span> has gone missing.<br> We know that our agent is alive, but we cannot contact him. Last time he  communicated with us, he mentioned that we could contact him at the  e-mail address the company has supplied him with, should there a problem  arise.<br> The problem is that when we last talked to him, he had not a company  e-mail address yet, but he told us that his e-mail can be found through  the company's site.<br> The only thing we remember is that he was hired on <span style='color:red;'>Friday </span>the <span style='color:red;'> 13th! </span><br> You have to find his e-mail address and send it to us by using the input filed given below.<br> Good luck!!!<br><br>";
					$data['level'] = $level;
					$this->load->view('levels', $data);
				} else {
					if($flag == 'Friday13@Jason.com'){
						$update_data = $this->level_model->update_status($id, $level);
						$this->session->set_userdata('level', $level);
						redirect(base_url('level'));
					}
					else{
						$data['text'] = "<b>Level 7</b><br><br>Our agents (hackers) informed us that there reasonable suspicion that  the site of this <a href=".base_url('')."level_9/ style='color:red;' target='_blank'>Logistics Company</a> is a blind for a human organs'  smuggling organisation.<br> This organisation attracts its victims through advertisments for jobs  with very high salaries. They choose those ones who do not have many  relatives, they assasinate them and then sell their organs to very rich  clients, at very high prices.<br> These employees are registered in the secret files of the company as 'special clients'!<br> One of our agents has been hired as by the particular company. Unfortunately, since 01/01/2017 <span style='color:red;'> Jason </span> has gone missing.<br> We know that our agent is alive, but we cannot contact him. Last time he  communicated with us, he mentioned that we could contact him at the  e-mail address the company has supplied him with, should there a problem  arise.<br> The problem is that when we last talked to him, he had not a company  e-mail address yet, but he told us that his e-mail can be found through  the company's site.<br> The only thing we remember is that he was hired on <span style='color:red;'>Friday </span>the <span style='color:red;'> 13th! </span><br> You have to find his e-mail address and send it to us by using the input filed given below.<br> Good luck!!!<br><br><p style='color:yellow;'>Errrhh! Try Again!</p>";
						$data['level'] = $level;
						$this->load->view('levels', $data);
					}
				}

				break;

			// case '8': //encrytion : sum of numbers == ascii
			// 	$this->form_validation->set_rules('flag', 'Answer', 'required');
			// 	if ($this->form_validation->run() == FALSE) {
			// 		$data['text'] = '<b>Level 8</b><br><br>Hello esteemed hacker, I hope you have some decent cryptography skills. I have some text I need decrypted.<br>I have done some information gathering on my network and I have recovered some data. However, it is encrypted and I cannot seem to decode it using any of my basic decryption tools. I have narrowed it down to the algorithm used to encrypt it, but it is beyond my scope.<br>Enter text to encrypt it.<br>';
			// 		$data['level'] = $level;
			// 		$data['encrypted'] = '';
			// 		$this->load->view('levels', $data);
			// 	} else {
			// 		if($flag=='asciimaaki'){
			// 			$update_data = $this->level_model->update_status($id, $level);
			// 			$this->session->set_userdata('level', $level);
			// 			redirect(base_url('level'));
			// 		}
			// 		else{
			// 			$data['text'] = '<b>Level 8</b><br><br>Hello esteemed hacker, I hope you have some decent cryptography skills. I have some text I need decrypted.<br>I have done some information gathering on my network and I have recovered some data. However, it is encrypted and I cannot seem to decode it using any of my basic decryption tools. I have narrowed it down to the algorithm used to encrypt it, but it is beyond my scope.<br>Enter text to encrypt it.<br><p style="color:yellow;">Errrhh! Try Again!</p>';
			// 			$data['level'] = $level;
			// 			$data['encrypted'] = '';
			// 			$this->load->view('levels', $data);
			// 		}
			// 	}

			// 	break;

			case '8'://robots.txt
				$this->form_validation->set_rules('flag', 'Answer', 'required');
				if ($this->form_validation->run() == FALSE) {
					$data['text'] = '<b>Level 8</b><br><br><br><IMG SRC='.base_url('images/robot.jpg').'><br><br><br>';
					$data['level'] = $level;
					$data['encrypted'] = '';
					$this->load->view('levels', $data);
				} else {
					if($flag=='robotsarethefuture'){
						$update_data = $this->level_model->update_status($id, $level);
						$this->session->set_userdata('level', $level);
						redirect(base_url('complete'));
					}
					else{
						$data['text'] = '<b>Level 8</b><br><br><br><IMG SRC='.base_url('images/robot.jpg').'><br><br><br><p style="color:yellow;">Errrhh! Try Again!</p>';
						$data['level'] = $level;
						$data['encrypted'] = '';
						$this->load->view('levels', $data);
					}
				}
				break;

			case '9':
				$this->endlanding();
				break;

			default:
				# code...
				break;

		}
	}
	public function startlanding(){
		$data['msg'] = 1;
		$this->load->view('landing', $data);
	}

	public function endlanding(){
		$data['msg'] = 0;
		$this->load->view('landing', $data);
	}

	// public function level5Encrypt(){
	// 	if($this->input->post('submit')){
	// 		$c = $this->input->post('test');
	// 		$new='';
	// 		for($i=0; $i<strlen($c); ++$i){
	// 			$new .= chr(ord($c[$i])+$i);
	// 		}
	// 		$data['text'] = '<b>Level 5</b><br><br><br>Our blooming Network Security Analyst Sam has encrypted his password. The encryption system is publically available and can be accessed with this form:<br>Please enter a string to have it encrypted.<br>';
	// 		$data['level'] = $this->session->userdata('level')+1;
	// 		$data['encrypted'] = $new;
	// 		$this->load->view('levels', $data);
	// 		// echo $new;
	// 	}
	// }

	// public function level11Encrypt(){
	// 	if($this->input->post('submit')){
	// 		$c = $this->input->post('test');
	// 		$new='';
	// 		for($i=0; $i<strlen($c); ++$i){
	// 			$ascii = ord($c[$i]);
	// 			$first = rand(1,$ascii/2);
	// 			$int1 = $ascii-$first;
	// 			$second = rand(1,$int1/2);
	// 			$third = $int1-$second;
	// 			$new.='.'.$first.'.'.$second.'.'.$third;
	// 		}
	// 		$data['text'] = "<b>Level 10</b><br><br>Hello esteemed hacker, I hope you have some decent cryptography skills. I have some text I need decrypted.<br>I have done some information gathering on my network and I have recovered some data. However, it is encrypted and I cannot seem to decode it using any of my basic decryption tools. I have narrowed it down to the algorithm used to encrypt it, but it is beyond my scope.<br>Enter text to encrypt it.<br>";
	// 			$data['level'] = $this->session->userdata('level')+1;
	// 			$data['encrypted'] = $new;
	// 			$this->load->view('levels', $data);
	// 	}
	// }

	public function rules()
	{
		$this->load->view('rules');
	}

}

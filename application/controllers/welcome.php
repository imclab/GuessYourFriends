<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
  function __construct() {
    parent::__construct();
    $this->load->helper('url');
  }
  

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
  {
    $this->load->library('facebook');

    $user = $this->facebook->getUser();

    if ($user) {
      try {
        $data['user_profile'] = $this->facebook->api('/me');
        } catch (FacebookApiException $e) {
          $user = null;
        }
    }

    if ($user) {
      $data['logout_url'] = $this->facebook->getLogoutUrl();
    } else {
      $data['login_url'] = $this->facebook->getLoginUrl();
    }

    $this->load->view('view',$data);
  }

    public function logout() {
      $this->load->library('facebook');
      session_destroy();
      $this->facebook->destroySession();
      redirect('/welcome');
  }

    public function friends() {
      $this->load->library('facebook');

      $user = $this->facebook->getUser();

      if ($user) {
        try {
          if (!isset($_SESSION['score'])) {
            $_SESSION['score'] = 0;
            }
            if (isset($_SESSION['friend_array'])) {
              $this->friend_array = $_SESSION['friend_array'];
            } else {
              $this->full_friends = $this->facebook->api('/me/friends');
              $_SESSION['friend_array'] =  $this->full_friends['data'];
              $this->friend_array = $_SESSION['friend_array'];
            }
            $index_number = array_rand($this->friend_array, 1);
            $user_profile = $this->friend_array[$index_number];
            $_SESSION['name'] = $user_profile['name'];
            $id = $user_profile['id'];
            $picture_json = $this->facebook->api('/'.$id.'/picture', array('access_token' => $this->facebook->getAccessToken(), 'type' => 'large', 'redirect' => false));
            $data['url']= $picture_json['data']['url'];
            unset($this->friend_array[$index_number]);
            $_SESSION['friend_array'] = array_values($this->friend_array);
            $_SESSION['score'] = $_SESSION['score'] + 1;
            $data['score'] = $_SESSION['score'];
          } catch (FacebookApiException $e) {
            $user = null;
          }
        echo json_encode($data);
      }
      else {
        print "no";
      }
      
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

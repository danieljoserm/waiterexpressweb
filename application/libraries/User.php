<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class User 
{

	var $info=array();
	var $inpro=array();
	var $loggedin=false;
	var $u=null;
	var $p=null;
	var $oauth_provider = null;
	var $oauth_id = null;
	var $oauth_token = null;
	var $oauth_secret = null;

	public function __construct() 
	{
		$CI =& get_instance();
		$config = $CI->config->item("cookieprefix");
		$this->u = $CI->input->cookie($config . "un", TRUE);
		$this->p = $CI->input->cookie($config . "tkn", TRUE);

		$this->oauth_provider = $CI->input->cookie($config . "provider", TRUE);
		$this->oauth_id = $CI->input->cookie($config . "oauthid", TRUE);
		$this->oauth_token = $CI->input->cookie($config . "oauthtoken", TRUE);
		$this->oauth_secret = $CI->input->cookie($config . "oauthsecret", TRUE);
 		
		 $user = null;
		 $propietario = null;

 		$select = "usuarios.`id_usuario`, usuarios.`dni`, usuarios.`username`, usuarios.`email`, 
				usuarios.first_name, usuarios.last_name, usuarios.`online_timestamp`, usuarios.avatar,
				usuarios.email_notification, usuarios.aboutme, usuarios.points,
				usuarios.premium_time, usuarios.active, usuarios.activate_code,
				usuarios.user_role, usuarios.app_token, user_roles.name as ur_name,
				ciudad.id, ciudad.estadonombre,
				user_roles.admin, user_roles.admin_settings, 
				user_roles.admin_members, user_roles.admin_payment,
				user_roles.ID as user_role_id,";
 		
 		// Twitter
		if($this->oauth_provider === "twitter") {
			if($this->oauth_provider && $this->oauth_id &&
			  $this->oauth_token && $this->oauth_secret) {
			 	$user = $CI->db->select($select)
				->where("oauth_provider", $this->oauth_provider)
				->where("oauth_id", $this->oauth_id)
				->where("oauth_token", $this->oauth_token)
				->where("oauth_secret", $this->oauth_secret)
				->join("user_roles", "user_roles.ID = usuarios.user_role", "left outer")
				->join("ciudad", "ciudad.id = usuarios.id_ciudad")
				->get("usuarios"); 
			}
		}

		// Facebook
		if($this->oauth_provider === "facebook") {
			if($this->oauth_provider && $this->oauth_id &&
			  $this->oauth_token) {
			 	$user = $CI->db->select($select)
				->where("oauth_provider", $this->oauth_provider)
				->where("oauth_id", $this->oauth_id)
				->where("oauth_token", $this->oauth_token)
				->join("user_roles", "user_roles.ID = usuarios.user_role","left outer")
				->join("ciudad", "ciudad.id = usuarios.id_ciudad")
				->get("usuarios");
			}
		}

		// Google
		if($this->oauth_provider === "google") {
			if($this->oauth_provider && $this->oauth_id &&
			  $this->oauth_token) {
			 	$user = $CI->db->select($select)
				->where("oauth_provider", $this->oauth_provider)
				->where("oauth_id", $this->oauth_id)
				->where("oauth_token", $this->oauth_token)
				->join("user_roles", "user_roles.ID = usuarios.user_role", "left outer")
				->join("ciudad", "ciudad.id = usuarios.id_ciudad")
				->get("usuarios");
			}
		}

		if ($this->u && $this->p && empty($this->oauth_provider)) {
			$user = $CI->db->select($select)
			->where("usuarios.email", $this->u)->where("usuarios.token", $this->p)
			->join("user_roles", "user_roles.ID = usuarios.user_role", "left outer")
			->join("ciudad", "ciudad.id = usuarios.id_ciudad")
			->get("usuarios");
		}

		if($user !== null) {
			if ($user->num_rows() == 0) {
				$this->loggedin=false;
			} else {
				$this->loggedin=true;
				$this->info = $user->row();

				if( (empty($this->info->username) || empty($this->info->email)) && ($CI->router->fetch_class() != "register")) {
					redirect(site_url("register/add_username"));
				}

				if($this->info->online_timestamp < time() - 60*5) {
					$this->update_online_timestamp($this->info->id_usuario);
				}

				if ($this->info->user_role == -1) {
					$CI->load->helper("cookie");
					$this->loggedin = false;
					$CI->session->set_flashdata("globalmsg", "This account has been deactivated and can no longer be used.");
					delete_cookie($config . "un");
					delete_cookie($config . "tkn");
					redirect(site_url("login/banned"));
				}
			}
		}
	}

	public function getPassword() 
	{
		$CI =& get_instance();
		$user = $CI->db->select("usuarios.`password`")
		->where("id_usuario", $this->info->id_usuario)->get("usuarios");
		$user = $user->row();
		return $user->password;
	}

	public function update_online_timestamp($userid) 
	{
		$CI =& get_instance();
		$CI->db->where("id_usuario", $userid)->update("usuarios", array(
			"online_timestamp" => time()
			)
		);
	}

}

?>

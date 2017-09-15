<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("admin_model");
		$this->load->model("user_model");

		if (!$this->user->loggedin) $this->template->error(lang("error_1"));
		if (!isset($this->user->info->user_role_id) && 
			(!$this->user->info->admin && !$this->user->info->admin_settings 
				&& !$this->user->info->admin_members &&
				!$this->user->info->admin_payment)
			) {
			$this->template->error(lang("error_2"));
		}
	}


	public function index() 
	{	
		$this->template->loadData("activeLink", 
			array("admin" => array("general" => 1)));
		$this->template->loadContent("admin/index.php", array(
			)
		);

	}

	public function premium_users($page=0) 
	{
		$this->template->loadData("activeLink", 
			array("admin" => array("premium_users" => 1)));


		$page = intval($page);
		$users = $this->admin_model->get_premium_users($page);

		$this->load->library('pagination');
		$config['base_url'] = site_url("admin/premium_users");
		$config['total_rows'] = $this->admin_model
			->get_total_premium_users_count();
		$config['per_page'] = 20;
		$config['uri_segment'] = 2;

		include (APPPATH . "/config/page_config.php");

		$this->pagination->initialize($config); 

		$this->template->loadContent("admin/premium_users.php", array(
			"members" => $users
			)
		);
	}

	public function user_roles() 
	{
		if(!$this->user->info->admin) $this->template->error(lang("error_2"));
		$this->template->loadData("activeLink", 
			array("admin" => array("user_roles" => 1)));
		$roles = $this->admin_model->get_user_roles();
		$this->template->loadContent("admin/user_roles.php", array(
			"roles" => $roles
			)
		);
	}

	public function add_user_role_pro() 
	{
		if(!$this->user->info->admin) $this->template->error(lang("error_2"));

		$name = $this->common->nohtml($this->input->post("name"));
		if (empty($name)) $this->template->error(lang("error_64"));

		$admin = intval($this->input->post("admin"));
		$admin_settings = intval($this->input->post("admin_settings"));
		$admin_members = intval($this->input->post("admin_members"));
		$admin_payment = intval($this->input->post("admin_payment"));

		$this->admin_model->add_user_role(
			array(
				"name" =>$name,
				"admin" => $admin,
				"admin_settings" => $admin_settings,
				"admin_members" => $admin_members,
				"admin_payment" => $admin_payment
				)
			);
		$this->session->set_flashdata("globalmsg", lang("success_30"));
		redirect(site_url("admin/user_roles"));
	}

	public function edit_user_role($id) 
	{
		if(!$this->user->info->admin) $this->template->error(lang("error_2"));
		$id = intval($id);
		$role = $this->admin_model->get_user_role($id);
		if ($role->num_rows() == 0) $this->template->error(lang("error_65"));

		$this->template->loadData("activeLink", 
			array("admin" => array("user_roles" => 1)));

		$this->template->loadContent("admin/edit_user_role.php", array(
			"role" => $role->row()
			)
		);
	}

	public function edit_user_role_pro($id) 
	{
		if(!$this->user->info->admin) $this->template->error(lang("error_2"));
		$id = intval($id);
		$role = $this->admin_model->get_user_role($id);
		if ($role->num_rows() == 0) $this->template->error(lang("error_65"));

		$name = $this->common->nohtml($this->input->post("name"));
		if (empty($name)) $this->template->error(lang("error_64"));

		$admin = intval($this->input->post("admin"));
		$admin_settings = intval($this->input->post("admin_settings"));
		$admin_members = intval($this->input->post("admin_members"));
		$admin_payment = intval($this->input->post("admin_payment"));

		$this->admin_model->update_user_role($id, 
			array(
				"name" =>$name,
				"admin" => $admin,
				"admin_settings" => $admin_settings,
				"admin_members" => $admin_members,
				"admin_payment" => $admin_payment
				)
		);
		$this->session->set_flashdata("globalmsg", lang("success_31"));
		redirect(site_url("admin/user_roles"));
	}

	public function delete_user_role($id, $hash) 
	{
		if(!$this->user->info->admin) $this->template->error(lang("error_2"));
		if ($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$group = $this->admin_model->get_user_role($id);
		if ($group->num_rows() == 0) $this->template->error(lang("error_65"));

		$this->admin_model->delete_user_role($id);
		// Delete all user groups from member

		$this->session->set_flashdata("globalmsg", lang("success_32"));
		redirect(site_url("admin/user_roles"));
	}

	public function payment_logs($page = 0) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_payment) {
			$this->template->error(lang("error_2"));
		}

		$page = intval($page);
		$this->template->loadData("activeLink", 
			array("admin" => array("payment_logs" => 1)));

		$logs = $this->admin_model->get_payment_logs($page);

		$this->load->library('pagination');
		$config['base_url'] = site_url("admin/payment_logs");
		$config['total_rows'] = $this->admin_model
			->get_total_payment_logs_count();
		$config['per_page'] = 20;
		$config['uri_segment'] = 2;

		include (APPPATH . "/config/page_config.php");

		$this->pagination->initialize($config); 

		$this->template->loadContent("admin/payment_logs.php", array(
			"logs" => $logs
			)
		);
	}

	public function payment_plans() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_payment) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("payment_plans" => 1)));
		$plans = $this->admin_model->get_payment_plans();

		$this->template->loadContent("admin/payment_plans.php", array(
			"plans" => $plans
			)
		);
	}

	public function add_payment_plan() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_payment) {
			$this->template->error(lang("error_2"));
		}
		$name = $this->common->nohtml($this->input->post("name"));
		$desc = $this->common->nohtml($this->input->post("description"));
		$cost = abs($this->input->post("cost"));
		$color = $this->common->nohtml($this->input->post("color"));
		$fontcolor = $this->common->nohtml($this->input->post("fontcolor"));
		$days = intval($this->input->post("days"));

		$this->admin_model->add_payment_plan(array(
			"name" => $name,
			"cost" => $cost,
			"hexcolor" => $color,
			"days" => $days,
			"description" => $desc,
			"fontcolor" => $fontcolor
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_25"));
		redirect(site_url("admin/payment_plans"));
	}

	public function edit_payment_plan($id) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_payment) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("payment_plans" => 1)));
		$id = intval($id);
		$plan = $this->admin_model->get_payment_plan($id);
		if($plan->num_rows() == 0) $this->template->error(lang("error_61"));

		$this->template->loadContent("admin/edit_payment_plan.php", array(
			"plan" => $plan->row()
			)
		);
	}

	public function edit_payment_plan_pro($id) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_payment) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$plan = $this->admin_model->get_payment_plan($id);
		if($plan->num_rows() == 0) $this->template->error(lang("error_61"));

		$name = $this->common->nohtml($this->input->post("name"));
		$desc = $this->common->nohtml($this->input->post("description"));
		$cost = abs($this->input->post("cost"));
		$color = $this->common->nohtml($this->input->post("color"));
		$fontcolor = $this->common->nohtml($this->input->post("fontcolor"));
		$days = intval($this->input->post("days"));

		$this->admin_model->update_payment_plan($id, array(
			"name" => $name,
			"cost" => $cost,
			"hexcolor" => $color,
			"days" => $days,
			"description" => $desc,
			"fontcolor" => $fontcolor
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_26"));
		redirect(site_url("admin/payment_plans"));
	}

	public function delete_payment_plan($id, $hash) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_payment) {
			$this->template->error(lang("error_2"));
		}
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}

		$id = intval($id);
		$plan = $this->admin_model->get_payment_plan($id);
		if($plan->num_rows() == 0) $this->template->error(lang("error_61"));

		$this->admin_model->delete_payment_plan($id);
		$this->session->set_flashdata("globalmsg", lang("success_27"));
		redirect(site_url("admin/payment_plans"));
	}

	public function payment_settings() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_payment) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("payment_settings" => 1)));
		$this->template->loadContent("admin/payment_settings.php", array(
			)
		);
	}

	public function payment_settings_pro() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_payment) {
			$this->template->error(lang("error_2"));
		}
		$paypal_email = $this->common->nohtml(
			$this->input->post("paypal_email"));
		$paypal_currency = $this->common->nohtml(
			$this->input->post("paypal_currency"));
		$payment_enabled = intval($this->input->post("payment_enabled"));
		$payment_symbol = $this->common->nohtml(
			$this->input->post("payment_symbol"));
		$global_premium = intval($this->input->post("global_premium"));

		// update
		$this->admin_model->updateSettings(
			array(
				"paypal_email" => $paypal_email,
				"paypal_currency" => $paypal_currency,
				"payment_enabled" => $payment_enabled,
				"payment_symbol" => $payment_symbol,
				"global_premium" => $global_premium
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_24"));
		redirect(site_url("admin/payment_settings"));

	}

	public function email_members() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("email_members" => 1)));
		$groups = $this->admin_model->get_user_groups();
		$this->template->loadContent("admin/email_members.php", array(
			"groups" => $groups
			)
		);
	}

	public function email_members_pro() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$usernames = $this->common->nohtml($this->input->post("usernames"));
		$groupid = intval($this->input->post("groupid"));
		$title = $this->common->nohtml($this->input->post("title"));
		$message = $this->lib_filter->go($this->input->post("message"));

		if ($groupid == -1) {
			// All members
			$users = array();
			$usersc = $this->admin_model->get_all_users();
			foreach ($usersc->result() as $r) {
				$users[] = $r;
			}
		} else {
			$usernames = explode(",", $usernames);

			$users = array();
			foreach ($usernames as $username) {
				if (empty($username)) continue;
				$user = $this->user_model->get_user_by_username($username);
				if ($user->num_rows() == 0) {
					$this->template->error(lang("error_3") . $username);
				}
				$users[] = $user->row();
			}

			if ($groupid > 0) {
				$group = $this->admin_model->get_user_group($groupid);
				if ($group->num_rows() == 0) {
					$this->template->error(lang("error_4"));
				}

				$users_g = $this->admin_model->get_all_group_users($groupid);
				$cursers = $users;

				foreach ($users_g->result() as $r) {
					// Check for duplicates
					$skip = false;
					foreach ($cusers as $a) {
						if($a->userid == $r->userid) $skip = true;
					}
					if (!$skip) {
						$users[] = $r;
					}
				}
			}

		}

		foreach ($users as $r) {
			$this->common->send_email($title, $message, $r->email);
		}

		$this->session->set_flashdata("globalmsg", lang("success_1"));
		redirect(site_url("admin/email_members"));
	}

	public function user_groups() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("user_groups" => 1)));
		$groups = $this->admin_model->get_user_groups();
		$this->template->loadContent("admin/groups.php", array(
			"groups" => $groups
			)
		);
	}

	public function add_group_pro() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$name = $this->common->nohtml($this->input->post("name"));
		$default = intval($this->input->post("default_group"));
		if (empty($name)) $this->template->error(lang("error_5"));


		$this->admin_model->add_group(
			array(
				"name" =>$name,
				"default" => $default,
				)
			);
		$this->session->set_flashdata("globalmsg", lang("success_2"));
		redirect(site_url("admin/user_groups"));
	}

	public function edit_group($id) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$group = $this->admin_model->get_user_group($id);
		if ($group->num_rows() == 0) $this->template->error(lang("error_4"));

		$this->template->loadData("activeLink", 
			array("admin" => array("user_groups" => 1)));

		$this->template->loadContent("admin/edit_group.php", array(
			"group" => $group->row()
			)
		);
	}

	public function edit_group_pro($id) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$group = $this->admin_model->get_user_group($id);
		if ($group->num_rows() == 0) $this->template->error(lang("error_4"));

		$name = $this->common->nohtml($this->input->post("name"));
		$default = intval($this->input->post("default_group"));
		if (empty($name)) $this->template->error(lang("error_5"));

		$this->admin_model->update_group($id, 
			array(
				"name" =>$name,
				"default" => $default
				)
		);
		$this->session->set_flashdata("globalmsg", lang("success_3"));
		redirect(site_url("admin/user_groups"));
	}

	public function delete_group($id, $hash) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		if ($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$group = $this->admin_model->get_user_group($id);
		if ($group->num_rows() == 0) $this->template->error(lang("error_4"));

		$this->admin_model->delete_group($id);
		// Delete all user groups from member
		$this->admin_model->delete_users_from_group($id); 

		$this->session->set_flashdata("globalmsg", lang("success_4"));
		redirect(site_url("admin/user_groups"));
	}

	public function view_group($id, $page=0) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("user_groups" => 1)));
		$id = intval($id);
		$page = intval($page);
		$group = $this->admin_model->get_user_group($id);
		if ($group->num_rows() == 0) $this->template->error(lang("error_4"));

		$users = $this->admin_model->get_users_from_groups($id, $page);

		$this->load->library('pagination');
		$config['base_url'] = site_url("admin/view_group/" . $id);
		$config['total_rows'] = $this->admin_model
			->get_total_user_group_members_count($id);
		$config['per_page'] = 20;
		$config['uri_segment'] = 3;

		include (APPPATH . "/config/page_config.php");

		$this->pagination->initialize($config); 

		$this->template->loadContent("admin/view_group.php", array(
			"group" => $group->row(),
			"users" => $users,
			"total_members" => $config['total_rows']
			)
		);

	}

	public function add_user_to_group_pro($id) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$group = $this->admin_model->get_user_group($id);
		if ($group->num_rows() == 0) $this->template->error(lang("error_4"));

		$usernames = $this->common->nohtml($this->input->post("usernames"));
		$usernames = explode(",", $usernames);

		$users = array();
		foreach ($usernames as $username) {
			$user = $this->user_model->get_user_by_username($username);
			if($user->num_rows() == 0) $this->template->error(lang("error_3") 
				. $username);
			$users[] = $user->row();
		}

		foreach ($users as $user) {
			// Check not already a member
			$userc = $this->admin_model->get_user_from_group($user->id_usuario, $id);
			if ($userc->num_rows() == 0) {
				$this->admin_model->add_user_to_group($user->id_usuario, $id);
			}
		}

		$this->session->set_flashdata("globalmsg", lang("success_5"));
		redirect(site_url("admin/view_group/" . $id));
	}

	public function remove_user_from_group($userid, $id, $hash) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		if ($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$userid = intval($userid);
		$group = $this->admin_model->get_user_group($id);
		if ($group->num_rows() == 0) $this->template->error(lang("error_4"));

		$user = $this->admin_model->get_user_from_group($userid, $id);
		if ($user->num_rows() == 0) $this->template->error(lang("error_7"));

		$this->admin_model->delete_user_from_group($userid, $id);
		$this->session->set_flashdata("globalmsg", lang("success_6"));
		redirect(site_url("admin/view_group/" . $id));
	}

	public function email_templates() 
	{
		if(!$this->user->info->admin) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("email_templates" => 1)));
		$email_templates = $this->admin_model->get_email_templates();
		$this->template->loadContent("admin/email_templates.php", array(
			"email_templates" => $email_templates
			)
		);
	}

	public function edit_email_template($id) 
	{
		if(!$this->user->info->admin) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("email_templates" => 1)));
		$id = intval($id);
		$email_template = $this->admin_model->get_email_template($id);
		if ($email_template->num_rows() == 0) {
			$this->template->error(lang("error_8"));
		}

		$this->template->loadContent("admin/edit_email_template.php", array(
			"email_template" => $email_template->row()
			)
		);
	}

	public function edit_email_template_pro($id) 
	{
		if(!$this->user->info->admin) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("email_templates" => 1)));
		$id = intval($id);
		$email_template = $this->admin_model->get_email_template($id);
		if ($email_template->num_rows() == 0) {
			$this->template->error(lang("error_8"));
		}

		$title = $this->common->nohtml($this->input->post("title"));
		$message = $this->lib_filter->go($this->input->post("message"));

		if (empty($title) || empty($message)) {
			$this->template->error(lang("error_9"));
		}

		$this->admin_model->update_email_template($id, $title, $message);
		$this->session->set_flashdata("globalmsg", lang("success_7"));
		redirect(site_url("admin/email_templates"));
	}

	public function ipblock() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("ipblock" => 1)));

		$ipblock = $this->admin_model->get_ip_blocks();

		$this->template->loadContent("admin/ipblock.php", array(
			"ipblock" => $ipblock
			)
		);
	}

	public function add_ipblock() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$ip = $this->common->nohtml($this->input->post("ip"));
		$reason = $this->common->nohtml($this->input->post("reason"));

		if (empty($ip)) $this->template->error(lang("error_10"));

		$this->admin_model->add_ipblock($ip, $reason);
		$this->session->set_flashdata("globalmsg", lang("success_8"));
		redirect(site_url("admin/ipblock"));
	}

	public function delete_ipblock($id) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$ipblock = $this->admin_model->get_ip_block($id);
		if ($ipblock->num_rows() == 0) $this->template->error(lang("error_11"));

		$this->admin_model->delete_ipblock($id);
		$this->session->set_flashdata("globalmsg", lang("success_9"));
		redirect(site_url("admin/ipblock"));
	}

	public function members($col=0,$sort=0,$page=0) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("members" => 1)));
		$page = intval($page);
		$col = intval($col);
		$sort = intval($sort);

		$sort_o = $sort;
		$col_o = $col;

		// Pagination
		$config['base_url'] = site_url("admin/members/" . $col . "/" . $sort);

		if ($col == 1) {
			$col = "usuarios.username";
		} elseif ($col == 2) {
			$col = "usuarios.first_name";
		} elseif ($col == 3) {
			$col = "usuarios.user_role";
		} elseif ($col == 4) {
			$col = "usuarios.joined";
		} elseif ($col == 5) {
			$col = "usuarios.oauth_provider";
		} elseif ($col == 6) {
			$col = "usuarios.email";
		}

		if ($sort == 1) {
			$sort = "ASC";
		} else {
			$sort = "DESC";
		}

		$members = $this->user_model->get_members($page, $col, $sort);

		$this->load->library('pagination');

		$config['total_rows'] = $this->user_model
			->get_total_members_count();
		$config['per_page'] = 20;
		$config['uri_segment'] = 5;

		include (APPPATH . "/config/page_config.php");

		$this->pagination->initialize($config); 

		$user_roles = $this->admin_model->get_user_roles();

		$this->template->loadContent("admin/members.php", array(
			"members" => $members,
			"sort" => $sort_o,
			"col" => $col_o,
			"page" => $page,
			"user_roles" => $user_roles
			)
		);
	}

	public function search_member() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("members" => 1)));
		$search = $this->common->nohtml($this->input->post("search"));
		$option = intval($this->input->post("option"));

		if ($option == 0) {
			// username
			$members = $this->user_model->search_by_username($search);
		} elseif ($option == 1) {
			// Email
			$members = $this->user_model->search_by_email($search);
		} elseif ($option == 2) {
			// First Name
			$members = $this->user_model->search_by_first_name($search);
		} elseif ($option == 3) {
			// Last Name
			$members = $this->user_model->search_by_last_name($search);
		}

		if ($members->num_rows() == 0) $this->template->error(lang("error_12"));


		$this->template->loadContent("admin/search_members.php", array(
			"members" => $members,
			)
		);
	}

	public function edit_member($id) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("members" => 1)));
		$id = intval($id);

		$member = $this->user_model->get_user_by_id($id);
		if ($member->num_rows() ==0 ) $this->template->error(lang("error_13"));

		$user_groups = $this->user_model->get_user_groups($id);
		$user_roles = $this->admin_model->get_user_roles();

		$this->template->loadContent("admin/edit_member.php", array(
			"member" => $member->row(),
			"user_groups" => $user_groups,
			"user_roles" => $user_roles
			)
		);
	}

	public function edit_member_pro($id) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);

		$member = $this->user_model->get_user_by_id($id);
		if ($member->num_rows() ==0 ) $this->template->error(lang("error_13"));

		$member = $member->row();

		$this->load->model("register_model");
		$email = $this->input->post("email", true);
		$first_name = $this->common->nohtml(
			$this->input->post("first_name", true));
		$last_name = $this->common->nohtml(
			$this->input->post("last_name", true));
		$pass = $this->common->nohtml(
			$this->input->post("password", true));
		$username = $this->common->nohtml(
			$this->input->post("username", true));
		$user_role = intval($this->input->post("user_role"));
		$aboutme = $this->common->nohtml($this->input->post("aboutme"));
		$points = abs($this->input->post("credits"));
		$active = intval($this->input->post("active"));

		if (strlen($username) < 3) $this->template->error(lang("error_14"));

		if (!preg_match("/^[a-z0-9_]+$/i", $username)) {
			$this->template->error(lang("error_15"));
		}

		if ($username != $member->username) {
			if (!$this->register_model->check_username_is_free($username)) {
				 $this->template->error(lang("error_16"));
			}
		}

		if (!empty($pass)) {
			if (strlen($pass) <= 5) {
				 $this->template->error(lang("error_17"));
			}
			$pass = $this->common->encrypt($pass);
		} else {
			$pass = $member->password;
		}

		$this->load->helper('email');
		$this->load->library('upload');

		if (empty($email)) {
				$this->template->error(lang("error_18"));
		}

		if (!valid_email($email)) {
			$this->template->error(lang("error_19"));
		}

		if ($email != $member->email) {
			if (!$this->register_model->checkEmailIsFree($email)) {
				 $this->template->error(lang("error_20"));
			}
		}

		if($user_role != $member->user_role) {
			if(!$this->user->info->admin) {
				$this->template->error(lang("error_66"));
			}
		}
		if($user_role > 0) {
			$role = $this->admin_model->get_user_role($user_role);
			if($role->num_rows() == 0) $this->template->error(lang("error_65"));
		}

		if ($_FILES['userfile']['size'] > 0) {
				$this->upload->initialize(array( 
			       "upload_path" => $this->settings->info->upload_path,
			       "overwrite" => FALSE,
			       "max_filename" => 300,
			       "encrypt_name" => TRUE,
			       "remove_spaces" => TRUE,
			       "allowed_types" => "gif|jpg|png|jpeg|",
			       "max_size" => 1000,
			       "xss_clean" => TRUE,
			       "max_width" => 80,
			       "max_height" => 80
			    ));

			    if (!$this->upload->do_upload()) {
			    	$this->template->error(lang("error_21")
			    	.$this->upload->display_errors());
			    }

			    $data = $this->upload->data();

			    $image = $data['file_name'];
			} else {
				$image= $member->avatar;
			}

		$this->user_model->update_user($id, 
			array(
				"username" => $username,
				"email" => $email,
				"first_name" => $first_name,
				"last_name" => $last_name,
				"password" => $pass,
				"user_role" => $user_role,
				"avatar" => $image,
				"aboutme" => $aboutme,
				"points" => $points,
				"active" => $active
				)
		);
		$this->session->set_flashdata("globalmsg", lang("success_10"));
		redirect(site_url("admin/members"));
	}

	public function add_member_pro() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		$this->load->model("register_model");
		$email = $this->input->post("email", true);
		$first_name = $this->common->nohtml(
			$this->input->post("first_name", true));
		$last_name = $this->common->nohtml(
			$this->input->post("last_name", true));
		$pass = $this->common->nohtml(
			$this->input->post("password", true));
		$pass2 = $this->common->nohtml(
			$this->input->post("password2", true));
		$captcha = $this->input->post("captcha", true);
		$username = $this->common->nohtml(
			$this->input->post("username", true));
		$user_role = intval($this->input->post("user_role"));

		if($user_role > 0) {
			$role = $this->admin_model->get_user_role($user_role);
			if($role->num_rows() == 0) $this->template->error(lang("error_65"));
			$role = $role->row();
			if($role->admin || $role->admin_members || $role->admin_settings 
				|| $role->admin_payment) {
				if(!$this->user->info->admin) {
					$this->template->error(lang("error_67"));
				}
			}
		}


		if (strlen($username) < 3) $this->template->error(lang("error_14"));

		if (!preg_match("/^[a-z0-9_]+$/i", $username)) {
			$this->template->error(lang("error_15"));
		}

		if (!$this->register_model->check_username_is_free($username)) {
			 $this->template->error(lang("error_16"));
		}

		if ($pass != $pass2) $this->template->error(lang("error_22"));

		if (strlen($pass) <= 5) {
			 $this->template->error(lang("error_17"));
		}

		$this->load->helper('email');

		if (empty($email)) {
				$this->template->error(lang("error_18"));
		}

		if (!valid_email($email)) {
			$this->template->error(lang("error_19"));
		}

		if (!$this->register_model->checkEmailIsFree($email)) {
			 $this->template->error(lang("error_20"));
		}

		$pass = $this->common->encrypt($pass);
		$this->register_model->add_user(array(
			"username" => $username,
			"email" => $email,
			"first_name" => $first_name,
			"last_name" => $last_name,
			"password" => $pass,
			"user_role" => $user_role,
			"IP" => $_SERVER['REMOTE_ADDR'],
			"joined" => time(),
			"joined_date" => date("n-Y"),
			"active" => 1
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_11"));
		redirect(site_url("admin/members"));
	
	}

	public function delete_member($id, $hash) 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_members) {
			$this->template->error(lang("error_2"));
		}
		if ($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$member = $this->user_model->get_user_by_id($id);
		if ($member->num_rows() ==0 ) $this->template->error(lang("error_13"));

		$this->user_model->delete_user($id);
		$this->session->set_flashdata("globalmsg", lang("success_12"));
		redirect(site_url("admin/members"));
	}

	public function social_settings() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_settings) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("social_settings" => 1)));
		$this->template->loadContent("admin/social_settings.php", array(
			)
		);
	}

	public function social_settings_pro() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_settings) {
			$this->template->error(lang("error_2"));
		}
		$disable_social_login = 
			intval($this->input->post("disable_social_login"));
		$twitter_consumer_key = 
			$this->common->nohtml($this->input->post("twitter_consumer_key"));
		$twitter_consumer_secret = 
			$this->common->nohtml($this->input->post("twitter_consumer_secret"));
		$facebook_app_id = 
			$this->common->nohtml($this->input->post("facebook_app_id"));
		$facebook_app_secret = 
			$this->common->nohtml($this->input->post("facebook_app_secret"));
		$google_client_id = 
			$this->common->nohtml($this->input->post("google_client_id"));
		$google_client_secret = 
			$this->common->nohtml($this->input->post("google_client_secret"));

		$this->admin_model->updateSettings(
			array(
				"disable_social_login" => $disable_social_login,
				"twitter_consumer_key" => $twitter_consumer_key,
				"twitter_consumer_secret" => $twitter_consumer_secret,
				"facebook_app_id" => $facebook_app_id, 
				"facebook_app_secret"=> $facebook_app_secret,  
				"google_client_id" => $google_client_id,
				"google_client_secret" => $google_client_secret,
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_13"));
		redirect(site_url("admin/social_settings"));
	}

	public function settings() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_settings) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("admin" => array("settings" => 1)));
		$this->template->loadContent("admin/settings.php", array(
			)
		);
	}

	public function settings_pro() 
	{
		if(!$this->user->info->admin && !$this->user->info->admin_settings) {
			$this->template->error(lang("error_2"));
		}
		$site_name = $this->common->nohtml($this->input->post("site_name"));
		$site_desc = $this->common->nohtml($this->input->post("site_desc"));
		$site_email = $this->common->nohtml($this->input->post("site_email"));
		$upload_path = $this->common->nohtml($this->input->post("upload_path"));
		$file_types = $this->common
			->nohtml($this->input->post("file_types"));
		$file_size = intval($this->input->post("file_size"));
		$upload_path_rel = 
			$this->common->nohtml($this->input->post("upload_path_relative"));
		$register = intval($this->input->post("register"));
		$avatar_upload = intval($this->input->post("avatar_upload"));
		$disable_captcha = intval($this->input->post("disable_captcha"));
		$date_format = $this->common->nohtml($this->input->post("date_format"));
		$login_protect = intval($this->input->post("login_protect"));
		$activate_account = intval($this->input->post("activate_account"));

		// Validate
		if (empty($site_name) || empty($site_email)) {
			$this->template->error(lang("error_23"));
		}
		$this->load->library("upload");

		if ($_FILES['userfile']['size'] > 0) {
			$this->upload->initialize(array( 
		       "upload_path" => $this->settings->info->upload_path,
		       "overwrite" => FALSE,
		       "max_filename" => 300,
		       "encrypt_name" => TRUE,
		       "remove_spaces" => TRUE,
		       "allowed_types" => $this->settings->info->file_types,
		       "max_size" => 2000,
		       "xss_clean" => TRUE
		    ));

		    if (!$this->upload->do_upload()) {
		    	$this->template->error(lang("error_21") 
		    	.$this->upload->display_errors());
		    }

		    $data = $this->upload->data();

		    $image = $data['file_name'];
		} else {
			$image= $this->settings->info->site_logo;
		}

		$this->admin_model->updateSettings(
			array(
				"site_name" => $site_name,
				"site_desc" => $site_desc,
				"upload_path" => $upload_path,
				"upload_path_relative" => $upload_path_rel, 
				"site_logo"=> $image,  
				"site_email" => $site_email,
				"register" => $register,
				"avatar_upload" => $avatar_upload,
				"file_types" => $file_types,
				"disable_captcha" => $disable_captcha,
				"date_format" => $date_format,
				"file_size" => $file_size,
				"login_protect" => $login_protect,
				"activate_account" => $activate_account
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_13"));
		redirect(site_url("admin/settings"));
	}

}

?>
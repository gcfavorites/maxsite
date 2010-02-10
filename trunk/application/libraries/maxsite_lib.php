<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */

class Maxsite_lib 
{
	var $version = '0.29';
	var $config = array();
	var $data = array();
	var $hooks = array();
	var $active_plugins = array();
	var $sidebars = array();
	var $widgets = array();
	var $title = '';
	var $description = '';
	var $keywords = '';
	var $language = false;
	var $current_lang_dir = false;
	
	
	function Maxsite_lib() 
	{
		$CI =& get_instance();
		
		$this->config['site_url'] = $CI->config->config['base_url'];
		$this->config['application_url'] = $this->config['site_url'] . APPPATH;
		$this->config['base_url'] = $this->config['site_url'] . APPPATH . 'maxsite/';
		$this->config['common_url'] = $this->config['base_url'] . 'common/';
		$this->config['templates_url'] = $this->config['base_url'] . 'templates/';
		$this->config['plugins_url'] = $this->config['base_url'] . 'plugins/';
		$this->config['admin_plugins_url'] = $this->config['base_url'] . 'admin/plugins/';
		$this->config['uploads_url'] = $this->config['site_url'] . 'uploads/';
		$this->config['admin_url'] = $this->config['base_url'] . 'admin/';
		$this->config['site_admin_url'] = $this->config['site_url'] . 'admin/';
		$this->config['base_dir'] = realpath(dirname(FCPATH)) . '/' . APPPATH . 'maxsite/';
		$this->config['application_dir'] = realpath(dirname(FCPATH)) . '/' . APPPATH;
		$this->config['common_dir'] = $this->config['base_dir'] . 'common/';
		$this->config['templates_dir'] = $this->config['base_dir'] . 'templates/';
		$this->config['plugins_dir'] = $this->config['base_dir'] . 'plugins/';
		$this->config['admin_plugins_dir'] = $this->config['base_dir'] . 'admin/plugins/';
		$this->config['uploads_dir'] = realpath(dirname(FCPATH)) . '/uploads/';
		$this->config['admin_dir'] = $this->config['base_dir'] . 'admin/';
		$this->config['config_file'] = $this->config['base_dir'] . 'mso_config.php';
		
		$this->config['cache_time'] = 86400; // в секундах = 24 часа
		$this->config['template'] = 'default';
		
		$this->config['secret_key'] = $this->config['site_url'];
		$this->config['remote_key'] = '0'; // ключ удаленного постинга
		
		$this->config['cache_dir'] = $CI->config->config['cache_path'];
		if (!$this->config['cache_dir'])
			$this->config['cache_dir'] = realpath(dirname(FCPATH)) . '/system/cache/';
	}
}

global $MSO;

if ( !isset($MSO) ) $MSO = new Maxsite_lib();

require_once( $MSO->config['common_dir'] . 'common.php' );

?>
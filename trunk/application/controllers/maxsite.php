<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */

class Maxsite extends Controller 
{
	
	var $data_def = array();
	
	function Maxsite()
	{
		parent::Controller();
		
		# подключаем свою библиотеку
		$this->load->library('maxsite_lib');
		
		# получаем к своему массиву параметров текущий URI
		$this->data_def['uri_segment'] = $this->uri->segment_array();
		
		# проверяем rss
		if ( ( count($this->data_def['uri_segment']) > 0 ) and 
			 ( $this->data_def['uri_segment'][count($this->data_def['uri_segment'])] == 'feed' )
			)
			$this->data_def['is_feed'] = '1';
		else
			$this->data_def['is_feed'] = '0';
			
		# инициализация + проверка залогиненности
		mso_initalizing();

		$this->data_def['session'] = $this->session->userdata;
		//$this->session->sess_destroy(); // для тестирования - обнуление сессии
	}
	
	function _remap($method)
	{	
		if (
			($method == 'home') or
			($method == 'archive') or
			($method == 'author') or
			($method == 'category') or
			($method == 'link') or
			($method == 'page') or
			($method == 'users') or
			($method == 'search') or
			($method == 'tag') or
			($method == 'home') or
			($method == 'comments') or
			($method == 'loginform')
			)
		{
			$this->_view_i($method);
		}
		elseif ($method == 'index') $this->index();
		elseif ($method == 'feed') $this->index('home');
		elseif ($method == 'install') $this->install();
		elseif ($method == 'admin') $this->_view_i('admin', 'admin');
		elseif ($method == 'url') $this->_view_i('url', 'url/url');
		elseif ($method == 'xmlrpc') $this->_view_i('xmlrpc', 'xmlrpc/xmlrpc');
		elseif ($method == 'xmlrpc_server') $this->_view_i('xmlrpc_server', 'xmlrpc/xmlrpc_server');
		// elseif ($method == 'trackback') $this->_view_i('trackback', 'xmlrpc/trackback');
		// elseif ($method == 'ping') $this->_view_i('ping', 'xmlrpc/ping');
		elseif ($method == 'login') $this->_view_i('login', 'login');
		elseif ($method == 'logout') $this->_view_i('logout', 'logout');
		else $this->page_404();
	}
	
	function _view_i($type = 'home', $vievers = 'index')
	{
		global $MSO;
		$data = array('type'=>$type);
		$MSO->data = array_merge($this->data_def, $data);
		
		if (function_exists('mso_autoload_plugins')) mso_autoload_plugins();
		
		mso_hook('init');
		
		$this->load->view($vievers, $MSO->data);
	}
	
	function page_404()
	{
		# если страница не определена здесь, то 
		# возможно существует расширение 
		# для этого подключим нужный файл если есть
		
		if ( count($this->data_def['uri_segment']) > 1 )
			$fn = APPPATH . 'controllers/' . $this->data_def['uri_segment'][2] . EXT;
		else 
			$fn = APPPATH . 'controllers/' . $this->data_def['uri_segment'][1] . EXT;
		
		if ( file_exists($fn) ) 
			require($fn);
		else 
		{
			# проверим короткую ссылку - может быть это slug из page или category 
			# если это так, то выставить тип вручную
			
			$slug = $this->data_def['uri_segment'][1]; // первый сегмент

			$this->db->select('page_id');
			$this->db->where(array('page_slug'=>$slug));
			$this->db->or_where(array('page_id'=>$slug));
			$this->db->limit('1');
			
			$query = $this->db->get('page');
			if ($query->num_rows() > 0) // есть страница
			{
				# добавим недостающий сегмент в uri_segment
				array_unshift($this->data_def['uri_segment'], 'page');
				
				// в этом массиве индексы начинаются с 1, а не 0 переделываем
				$out = array();

				foreach ($this->data_def['uri_segment'] as $key => $val)
						$out[$key + 1] = $val;

				$this->data_def['uri_segment'] = $out;
				
				$this->_view_i('page');
			}
			else 
			{
				// теперь тоже самое, только с рубрикой
				$this->db->select('category_id');
				$this->db->where(array('category_slug'=>$slug));
				$query = $this->db->get('category');
				if ($query->num_rows() > 0) // есть рубрика
				{
					array_unshift($this->data_def['uri_segment'], 'category');
					$out = array();
					foreach ($this->data_def['uri_segment'] as $key => $val)
							$out[$key + 1] = $val;

					$this->data_def['uri_segment'] = $out;
					$this->_view_i('category');
				}
				else 
				{
					$this->_view_i('page_404');
				}
			}
		}
	}
	
	function index()
	{
		global $mso_install;
		
		if ($mso_install == false)
		{
			$CI = & get_instance();	
			if ( !$CI->db->table_exists('options')) return $this->install();
		}
		
		$this->_view_i('home');
	}
	
	function install()
	{
		global $MSO, $mso_install;
		
		
		if ($mso_install == true) 
		{
			$this->_view_i('home');
			return;
		}
		
		$CI = & get_instance();
		if ($CI->db->table_exists('options')) // echo 'уже есть';
		{
			$this->_view_i('home');
			return;
		}
		
		$css = $CI->config->config['base_url'] . APPPATH . 'views/install/install.css';
		
		if ( ( count($this->data_def['uri_segment']) > 0 ) and 
			 ( $this->data_def['uri_segment'][count($this->data_def['uri_segment'])] == '2' )
			)
			$step = 2;
		else
			$step = 1;

		$data = array('type'=>'install', 'url_css'=>$css, 'step'=>$step);
		$MSO->data = array_merge($this->data_def, $data);
		$this->load->view('install/install', $MSO->data);
	}
	
}
?>
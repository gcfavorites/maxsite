<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// pr($MSO);

# ���������� ��� � �������� html - ������ ���� ������ � ����� �� ������ (777)!
if ( mso_get_option('global_cache', 'templates', false) ) // ���� ��������� � ������ �������
{
	//$cache_key = mso_md5($_SERVER['REQUEST_URI']);
	$cache_key = $_SERVER['REQUEST_URI'];
	$cache_key = str_replace('/', '-', $cache_key);
	$cache_key = mso_slug(' ' . $cache_key);
	$cache_key = 'html/' . $cache_key . '.html';

	if ( $k = mso_get_cache($cache_key, true) ) return print($k); // �� ���� � ����
	ob_start();
}

if ( is_feed() )
{
	# ��� rss ������������ ������ �������
	if ( is_type('page') ) require('feed-page.php'); 					// ������ ����������� � ��������
		elseif ( is_type('comments') ) require('feed-comments.php');	// ��� �����������
	#	elseif ( is_type('archive') ) require('feed-archive.php'); 		// �� ����� - ������???
		elseif ( is_type('category') ) require('feed-category.php'); 	// �� ��������
	#	elseif ( is_type('tag') ) require('feed-tag.php'); 				// �� ������
		else require('feed-home.php'); // ��� ������					// ��� ��������
	
	exit; // �������
}

# ���������� ������ ���������� - ��� ������������ ����� �����
require_once( getinfo('common_dir') . 'page.php' ); 			// ������� ������� 
require_once( getinfo('common_dir') . 'category.php' ); 		// ������� ������

# � ����������� �� ���� ������ ���������� ������ ����
if ( is_type('archive') ) 			require('archive.php');		// ����� �� �����
	elseif ( is_type('home') ) 		require('home.php');		// �������
	elseif ( is_type('page') ) 		require('page.php');		// �������� 
	elseif ( is_type('comments') ) 	require('comments.php');	// ��� �����������
	elseif ( is_type('loginform') )	require('loginform.php');	// ����� ������
	elseif ( is_type('contact') ) 	require('contact.php');		// ���������� �����
	elseif ( is_type('category') )	require('category.php');	// �������
	elseif ( is_type('search') )	require('search.php');		// �����
	elseif ( is_type('tag') )		require('tag.php');			// �����
	# elseif ( is_type('author') ) 	require('author.php');
	# elseif ( is_type('link') )	require('link.php');
	elseif ( is_type('users') )	
	{
		if (mso_segment(3)=='edit')	require('users-form.php');
		else require('users.php');
	}
	else 							require('page_404.php');	// 404 - ���� �� �������

# ���������� ��� �� 300 ������ = 5 �����
if ( mso_get_option('global_cache', 'templates', false) ) mso_add_cache($cache_key, ob_get_flush(), 300, true);

?>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

/*

обновление до 0.40

ALTER TABLE `mso_category` DROP INDEX `category_type`;
ALTER TABLE `mso_page_type` DROP INDEX `page_type_name`;
ALTER TABLE `mso_comusers` DROP INDEX `comusers_email`;
ALTER TABLE `mso_comusers` DROP INDEX `comusers_nik`;
ALTER TABLE `mso_groups` DROP INDEX `groups_name`;
ALTER TABLE `mso_users` DROP INDEX `users_nik`;
ALTER TABLE `mso_category` ADD INDEX `category_id_parent` ( `category_id_parent` );
ALTER TABLE `mso_meta` ADD INDEX `meta_value` ( `meta_value` ( 256 ) );
ALTER TABLE `mso_cat2obj` ADD INDEX `page_id` ( `page_id` )

- page_title 
- page_content 
- page_content2 

*/

$this->db->query('ALTER TABLE `' . $this->db->dbprefix('category') . '` DROP INDEX `category_type`');
$this->db->query('ALTER TABLE `' . $this->db->dbprefix('page_type') . '` DROP INDEX `page_type_name`;');
$this->db->query('ALTER TABLE `' . $this->db->dbprefix('comusers') . '` DROP INDEX `comusers_email`;');
$this->db->query('ALTER TABLE `' . $this->db->dbprefix('comusers') . '` DROP INDEX `comusers_nik`;');
$this->db->query('ALTER TABLE `' . $this->db->dbprefix('groups') . '` DROP INDEX `groups_name`;');
$this->db->query('ALTER TABLE `' . $this->db->dbprefix('users') . '` DROP INDEX `users_nik`;');

$this->db->query('ALTER TABLE `' . $this->db->dbprefix('page') . '` DROP INDEX `page_title`;');
$this->db->query('ALTER TABLE `' . $this->db->dbprefix('page') . '` DROP INDEX `page_content`;');
$this->db->query('ALTER TABLE `' . $this->db->dbprefix('page') . '` DROP INDEX `page_content2`;');

$this->db->query('ALTER TABLE `' . $this->db->dbprefix('category') . '` ADD INDEX `category_id_parent` ( `category_id_parent` );');
$this->db->query('ALTER TABLE `' . $this->db->dbprefix('meta') . '` ADD INDEX `meta_value` ( `meta_value` ( 256 ) );');
$this->db->query('ALTER TABLE `' . $this->db->dbprefix('cat2obj') . '` ADD INDEX `page_id` ( `page_id` )');

die ('Обновление выполнено!');


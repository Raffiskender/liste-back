<?php

namespace Liste_de_course\Models;

class Correspondance extends CoreModel
{
    public const TABLE_NAME = 'correspondance';
		
    public function createTable()
    {
         $charset_collate = $this->wpdb->get_charset_collate();
         $sql = 'CREATE TABLE `' . self::TABLE_NAME . "` (
			   `list_element_id` int unsigned NOT NULL,
			   `rubrique_id` int unsigned NOT NULL
			  ) " . $charset_collate;
	
		//var_dump($sql); die;
		$this->wpdb->query( $sql );
	}
	
	public function deleteTable()
	{
		$sql = 'DROP TABLE `' . self::TABLE_NAME . '`';
		$this->wpdb->query($sql);
	}
	
	public function deleteOneListElement($params)
	{
	  $listElementId = $params["listElementId"];
	
    $sql = "DELETE * FROM `" . SELF::TABLE_NAME . "` WHERE `list_element_id` =" . $listElementId;
		
    return $this->wpdb->query($sql);
  }
	
	public function deleteOneRubrique($params)
	{
	  $rubriqueId = $params["rubriqueId"];
	
    $sql = "DELETE * FROM `" . SELF::TABLE_NAME . "` WHERE `rubrique_id` =" . $rubriqueId;
	
    return $this->wpdb->query($sql);		
  }

	public function findAll()
	{
		$sql = "SELECT * FROM `" . self::TABLE_NAME . "`";
		return $this->wpdb->get_results($sql);
	}

	public function findByRubrique($params)
	{
	  $rubriqueId = $params["rubriqueId"];
		
		$sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE `rubrique_id` = " . $rubriqueId;
		return $this->wpdb->get_results($sql);
	}

	public function findByListElement($params)
	{
	  $listElementId = $params["listElementId"];
		
		$sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE `list_element_id` = " . $listElementId;
		return $this->wpdb->get_results($sql);
	}

	public function addNewCorrespondance($request)
	{
		$params = $request->get_params('listElementId', 'rubriqueId');
		$data = [
			'list_element_id'=>$params['listElementId'],
			'rubrique_id'    =>$params['rubriqueId']
		];
	  
		return $this->wpdb->insert(self::TABLE_NAME, $data);
	}
}
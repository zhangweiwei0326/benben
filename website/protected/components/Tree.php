<?php

class Tree{
	static public $treeList=array();//存放无限极分类的结果

	public function create($data,$parent_id=0){

		foreach ($data as $key => $value) {
			if ($value['parent_id']==$parent_id) {
				self::$treeList[]=$value;
				unset($data[$key]);
				self::create($data,$value['id']);
			}
		}

		return self::$treeList;
	}
}
?>
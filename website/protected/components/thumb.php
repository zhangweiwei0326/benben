<?php 
/**
 * 图像处理类
 */
class Image{
	private $file; //图片地址
	private $width;
	private $height;
	private $type;
	private $img; //原图资源句柄
	private $new; //新图的资源句柄
	private $name;
	private $path;
	
	public function __construct($file, $name){
		$this->file = $_SERVER['DOCUMENT_ROOT'].'/'.$file;
		list($this->width, $this->height, $this->type) = getimagesize($this->file);
		$this->img =$this->getFromImg($this->file, $this->type);;
		$this->path = substr($file, 0,strrpos($file, "/")+1).'small_'.substr($name, 1,strlen($name) - 1);
		$this->name = $_SERVER['DOCUMENT_ROOT'].'/'.$this->path;
	}
	
	//缩略图(固定长高容器，图像等比例，扩容填充，裁剪)[固定了大小，不失真，不变形]
	public function thumb($new_width,$new_height) {
			if (empty($new_width) && empty($new_height)) {
			$new_width = $this->width;
			$new_height = $this->height;
		}
		
		if (!is_numeric($new_width) || !is_numeric($new_height)) {
			$new_width = $this->width;
			$new_height = $this->height;
		}
		
		//创建一个容器
		$_n_w = $new_width;
		$_n_h = $new_height;
		
		//创建裁剪点
		$_cut_width = 0;
		$_cut_height = 0;
		
		if ($this->width < $this->height) {
			$new_width = ($new_height / $this->height) * $this->width;
		} else {
			$new_height = ($new_width / $this->width) * $this->height;
		}
	
		
		
		
		if ($new_width < $_n_w) { //如果新高度小于新容器高度
			$r = $_n_w / $new_width; //按长度求出等比例因子
			$new_width *= $r; //扩展填充后的长度
			$new_height *= $r; //扩展填充后的高度
			$_cut_height = ($this->height - $_n_w) / 4; //求出裁剪点的高度
		}
		
		if ($new_height < $_n_h) { //如果新高度小于容器高度
			$r = $_n_h / $new_height; //按高度求出等比例因子
			$new_width *= $r; //扩展填充后的长度
			$new_height *= $r; //扩展填充后的高度
			$_cut_width = ($this->width - $_n_h) / 4; //求出裁剪点的长度
		}
			
		
		$this->new = imagecreatetruecolor($_n_w,$_n_h);
		imagecopyresampled($this->new,$this->img,0,0,$_cut_width,$_cut_height,$new_width,$new_height,$this->width,$this->height);
	}
	
	private function getFromImg($file, $type){
		switch ($type){
			case 1:
				$img = imagecreatefromgif($file);
				break;
			case 2:
				$img = imagecreatefromjpeg($file);
				break;
			case 3:
				$img = imagecreatefrompng($file);
				break;	
		}
		return $img;
	}
	
	//图像输出
	public function out() {
		imagepng($this->new,$this->name);
		return $this->path;
		imagedestroy($this->img);
		imagedestroy($this->new);
	}
	
}

?>
<?php
/**
 * Created by PhpStorm.
 * User: gradus
 * Date: 28.03.15
 * Time: 21:50
 */
/**
 *  RUS DOC
 *
 # загрузит изображение image.jpg, изменить его ширину до 400 пикселей
 # и высоту до 200 пикселей, а затем сохранит как image1.jpg.
  	$image = new SimpleImage();
  	$image->load('image.jpg');
  	$image->resize(400, 200);
  	$image->save('image1.jpg');

 # Если необходимо изменить размеры изображения, основываясь только на ширине
 # и при этом сохранить его пропорции, то сценарий сам выберет необходимую высоту.
 # Для этого необходимо использовать метод resizeToWidth.
	$image = new SimpleImage();
  	$image->load('image.jpg');
  	$image->resizeToWidth(250);
  	$image->save('image1.jpg');

 # изменить размер в процентном соотношении от его оригинала.
 # Для этого существует метод scale, в качестве параметра которому передаются проценты.
	$image = new SimpleImage();
  	$image->load('image.jpg');
  	$image->scale(50);
  	$image->save('image1.jpg');

 # метод output выводит изображения , без предварительного сохранения.
	$image = new SimpleImage();
  	$image->load('image.jpg');
  	$image->resizeToWidth(150);
  	$image->output();

*/


class SimpleImage {

	var $image;
	var $image_type;

	function load($filename) {
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if( $this->image_type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}
	}
	function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$compression);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename);
		}
		if( $permissions != null) {
			chmod($filename,$permissions);
		}
	}
	function output($image_type=IMAGETYPE_JPEG) {
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image);
		}
	}
	function getWidth() {
		return imagesx($this->image);
	}
	function getHeight() {
		return imagesy($this->image);
	}
	function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}
	function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}
	function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);
	}
	function resize($width,$height) {
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
	}
}


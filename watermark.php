<?php
namespace ImageCopyright;

class Watermark {
	
	public $error = array();
	private $imgSource = null;
	private $imgWatermark = null;

	/**
	 * 
	 * Positions watermark
	 * 0: Centered
	 * 1: Top Left
	 * 2: Top Right
	 * 3: Footer Right
	 * 4: Footer left
	 * 5: Top Centered
	 * 6: Center Right
	 * 7: Footer Centered
	 * 8: Center Left
	 * @var number
	 */
	private $watermarkPosition = 0;
	
	public function __construct(){
		if(!function_exists("imagecreatetruecolor")){
			if(!function_exists("imagecreate")){
				$this->error[] = "You do not have the GD library loaded in PHP!";
			}
		}
	}

	/**
	 * 
	 * Get function name for use in apply
	 * @param string $name Image Name
	 * @param string $action |open|save|
	 */
	private function getFunction($name, $action = 'open') {
		if(preg_match("/^(.*)\.(jpeg|jpg)$/i", $name)){
			if($action == "open")
				return "imagecreatefromjpeg";
			else
				return "imagejpeg";
		}elseif(preg_match("/^(.*)\.(png)$/i", $name)){
			if($action == "open")
				return "imagecreatefrompng";
			else
				return "imagepng";
		}elseif(preg_match("/^(.*)\.(gif)$/i", $name)){
			if($action == "open")
				return "imagecreatefromgif";
			else
				return "imagegif";
		}else{
			$this->error[] = "Image Format Invalid!";
		}
	}

	public function getImgSizes($img){
		return array('width' => imagesx($img), 'height' => imagesy($img));
	}

	public function getPositions(){
		$imgSource = $this->getImgSizes($this->imgSource);
		$imgWatermark = $this->getImgSizes($this->imgWatermark);
		$positionX = 0;
		$positionY = 0;

		# Centered
		if($this->watermarkPosition == 0){
			$positionX = ( $imgSource['width'] / 2 ) - ( $imgWatermark['width'] / 2 );
			$positionY = ( $imgSource['height'] / 2 ) - ( $imgWatermark['height'] / 2 );
		}

		# Top Left
		if($this->watermarkPosition == 1){
			$positionX = 0;
			$positionY = 0;
		}

		# Top Right
		if($this->watermarkPosition == 2){
			$positionX = $imgSource['width'] - $imgWatermark['width'];
			$positionY = 0;
		}

		# Footer Right
		if($this->watermarkPosition == 3){
			$positionX = ($imgSource['width'] - $imgWatermark['width']) - 5;
			$positionY = ($imgSource['height'] - $imgWatermark['height']) - 5;
		}

		# Footer left
		if($this->watermarkPosition == 4){
			$positionX = 0;
			$positionY = $imgSource['height'] - $imgWatermark['height'];
		}

		# Top Centered
		if($this->watermarkPosition == 5){
			$positionX = ( ( $imgSource['height'] - $imgWatermark['width'] ) / 2 );
			$positionY = 0;
		}

		# Center Right
		if($this->watermarkPosition == 6){
			$positionX = $imgSource['width'] - $imgWatermark['width'];
			$positionY = ( $imgSource['height'] / 2 ) - ( $imgWatermark['height'] / 2 );
		}

		# Footer Centered
		if($this->watermarkPosition == 7){
			$positionX = ( ( $imgSource['width'] - $imgWatermark['width'] ) / 2 );
			$positionY = $imgSource['height'] - $imgWatermark['height'];
		}

		# Center Left
		if($this->watermarkPosition == 8){
			$positionX = 0;
			$positionY = ( $imgSource['height'] / 2 ) - ( $imgWatermark['height'] / 2 );
		}

		return array('x' => $positionX, 'y' => $positionY);
	}

	/**
	 * 
	 * Apply watermark in image
	 * @param string $imgSource Name image source
	 * @param string $imgTarget Name image target
	 * @param string $imgWatermark Name image watermark
	 * @param number $position Position watermark
	 */
	public function apply($imgSource, $imgTarget, $imgWatermark, $position = 0){
		$this->watermarkPosition = $position;

		$functionSource = $this->getFunction($imgSource, 'open');
		$this->imgSource = $functionSource($imgSource);

		$functionWatermark = $this->getFunction($imgWatermark, 'open');
		$this->imgWatermark = $functionWatermark($imgWatermark);
		
		$sizesWatermark = $this->getImgSizes($this->imgWatermark);

		$positions = $this->getPositions();

		// Apply watermark
		imagecopy($this->imgSource, $this->imgWatermark, $positions['x'], $positions['y'], 0, 0, $sizesWatermark['width'], $sizesWatermark['height']);

		// Get function name to use for save image
		$functionTarget = $this->getFunction($imgTarget, 'save');
		
		// Change default DPI from 96 to 72
		imageresolution($this->imgSource, 72);

		// Save image
		//$functionTarget($this->imgSource, $imgTarget); // Default quality
		
		if(preg_match('/^(.*)\.(jpeg|jpg)$/i', $imgTarget)){
			$functionTarget($this->imgSource, $imgTarget, 100);
		}elseif(preg_match('/^(.*)\.(png)$/i', $imgTarget)){
			// uncomment for transparency
			//imagealphablending($this->imgSource, false);
			//imagesavealpha($this->imgSource, true);
			$functionTarget($this->imgSource, $imgTarget, 0);
		}elseif(preg_match('/^(.*)\.(gif)$/i', $imgTarget)){
			$functionTarget($this->imgSource, $imgTarget);
		}

		imagedestroy($this->imgSource);
		imagedestroy($this->imgWatermark);
	}
}
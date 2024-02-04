<?php

/**
 * Image data object
 */
class ImageData
{
    /**
     * image's path
     *
     * @var string 
     */
    public $path;
    /**
     * image's thumbnail (base64 or path)
     *
     * @var string 
     */
    public $thumb;
}

/**
 * Image Helper
 *
 * @author johnatas
 * Based in: https://gist.github.com/miguelxt/908143
 */
class ImageHelper
{
    public $image;
    public $image_type;

    public function __construct($filename = null)
    {
        if(!empty($filename))
        {
            $this->load($filename);
        }
    }

    /**
     * Load the image (jpg,png,gif) contained in the filename
     * 
     * @param string $filename file with the image
     * @return void
     */
    public function load($filename): void
    {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if($this->image_type == IMAGETYPE_JPEG)
        {
            $this->image = imagecreatefromjpeg($filename);
        }
        else if($this->image_type == IMAGETYPE_GIF)
        {
            $this->image = imagecreatefromgif($filename);
        }
        else if($this->image_type == IMAGETYPE_PNG)
        {
            $this->image = imagecreatefrompng($filename);
        }
        else
        {
            throw new Exception("The file you're trying to open is not supported");
        }
    }

    /**
     * Save the image
     * 
     * @param string $filename file to save the image
     * @param int $image_type type of the image (jpg,png,gif)
     * @param int $compression compression rate
     * @param string $permissions unix permision to filename
     * @return void
     */
    public function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null): void
    {
        if($image_type == IMAGETYPE_JPEG)
        {
            imagejpeg($this->image, $filename, $compression);
        }
        else if($image_type == IMAGETYPE_GIF)
        {
            imagegif($this->image, $filename);
        }
        else if($image_type == IMAGETYPE_PNG)
        {
            imagepng($this->image, $filename);
        }
        if($permissions != null)
        {
            chmod($filename, $permissions);
        }
    }

    /**
     * Stream the image
     */
    public function output($image_type = IMAGETYPE_JPEG, $quality = 80): void
    {
        if($image_type == IMAGETYPE_JPEG)
        {
            header("Content-type: image/jpeg");
            imagejpeg($this->image, null, $quality);
        }
        else if($image_type == IMAGETYPE_GIF)
        {
            header("Content-type: image/gif");
            imagegif($this->image);
        }
        else if($image_type == IMAGETYPE_PNG)
        {
            header("Content-type: image/png");
            imagepng($this->image);
        }
    }

    /**
     * Get image's width
     */
    public function getWidth(): int
    {
        return imagesx($this->image);
    }

    /**
     * Get image's height
     */
    public function getHeight(): int
    {
        return imagesy($this->image);
    }

    /**
     * Resize the image to specified height
     */
    public function resizeToHeight($height): void
    {
        $ratio = $height / $this->getHeight();
        $width = round($this->getWidth() * $ratio);
        $this->resize($width, $height);
    }

    /**
     * Resize the image to specified width
     */
    public function resizeToWidth($width): void
    {
        $ratio = $width / $this->getWidth();
        $height = round($this->getHeight() * $ratio);
        $this->resize($width, $height);
    }

    /**
     * Ajust imagem into a square
     */
    public function square($size): void
    {
        $new_image = imagecreatetruecolor($size, $size);
        if($this->getWidth() > $this->getHeight())
        {
            $this->resizeToHeight($size);

            imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            imagecopy($new_image, $this->image, 0, 0, ($this->getWidth() - $size) / 2, 0, $size, $size);
        }
        else
        {
            $this->resizeToWidth($size);

            imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            imagecopy($new_image, $this->image, 0, 0, 0, ($this->getHeight() - $size) / 2, $size, $size);
        }
        $this->image = $new_image;
    }

    /**
     * Scale
     */
    public function scale($scale): void
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getHeight() * $scale / 100;
        $this->resize($width, $height);
    }

    /**
     * Resize
     */
    public function resize($width, $height): void
    {
        $new_image = imagecreatetruecolor($width, $height);

        imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);

        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

    /**
     * Cut
     */
    public function cut($x, $y, $width, $height): void
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        imagecopy($new_image, $this->image, 0, 0, $x, $y, $width, $height);
        $this->image = $new_image;
    }

    /**
     * Reduce image to the specified width and height
     */
    public function maxarea($width, $height = null): void
    {
        $height = $height ? $height : $width;

        if($this->getWidth() > $width)
        {
            $this->resizeToWidth($width);
        }
        if($this->getHeight() > $height)
        {
            $this->resizeToheight($height);
        }
    }

    /**
     * Scale image to the specified width and height
     */
    public function minarea($width, $height = null): void
    {
        $height = $height ? $height : $width;

        if($this->getWidth() < $width)
        {
            $this->resizeToWidth($width);
        }
        if($this->getHeight() < $height)
        {
            $this->resizeToheight($height);
        }
    }

    /**
     * Cut image by it's center
     */
    public function cutFromCenter($width, $height): void
    {

        if($width < $this->getWidth() && $width > $height)
        {
            $this->resizeToWidth($width);
        }
        if($height < $this->getHeight() && $width < $height)
        {
            $this->resizeToHeight($height);
        }

        $x = ($this->getWidth() / 2) - ($width / 2);
        $y = ($this->getHeight() / 2) - ($height / 2);

        $this->cut($x, $y, $width, $height);
    }

    /**
     * Fill the image
     */
    public function maxareafill($width, $height, $red = 0, $green = 0, $blue = 0): void
    {
        $this->maxarea($width, $height);
        $new_image = imagecreatetruecolor($width, $height);
        $color_fill = imagecolorallocate($new_image, $red, $green, $blue);
        imagefill($new_image, 0, 0, $color_fill);
        imagecopyresampled($new_image, $this->image, floor(($width - $this->getWidth()) / 2), floor(($height - $this->getHeight()) / 2), 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }
    
    /**
     * Convert a base64 encoded image to file and gerenate it's thumbnail as a file or base64 encoded image
     * 
     * @param string $base_64_img base64 encoded image
     * @param string $img_name image filename
     * @param string $path image's path
     * @param bool $generate_thumb flag to generate image's thumbnail [Optional. Default = true]
     * @param bool $thumb_as_file save thumb as file instead of base64 encoded image [Optional. Default = false]
     * @param int $width_thumb thumb image width in px [Optional. Default = 100]
     * @return \ImageData
     */
    public function generateFromBase64($base_64_img, $img_name, $path = '', $generate_thumb = true, $thumb_as_file = false, $width_thumb = 100): \ImageData
    {
        // converte para imagem 
        $id_file = NblPHPUtil::makeNumericId();
        $path = (empty($path)) ? RSC_PATH : $path;
        $arq_nome = $path . '/img_' . $id_file . '_'.$img_name;
        $arq_url = 'img_' . $id_file . '_'.$img_name;
        $arq_nome_thumb = $path . '/img_thumb_' . $id_file . '_'.$img_name;
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base_64_img));
        file_put_contents($arq_nome, $data);
        
        $ret = new ImageData();
        $ret->path = $arq_url;
        
        if($generate_thumb)
        {
            // gera o thumb
            $this->load($arq_nome);
            $this->resizeToWidth($width_thumb);
            $this->save($arq_nome_thumb);
            if(!$thumb_as_file)
            {
                // gera o base64 do thumb
                $type = pathinfo($arq_nome_thumb, PATHINFO_EXTENSION);
                $data = file_get_contents($arq_nome_thumb);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                @unlink($arq_nome_thumb);
                $ret->thumb = $base64;
            }
            else 
            {
                $ret->thumb = $arq_nome_thumb;
            }
        }
        else
        {
            $ret->thumb = '';
        }
        
        return $ret;
    }

}

?>

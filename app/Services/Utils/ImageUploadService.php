<?php

namespace App\Utils;

use Illuminate\Http\UploadedFile;
use App\Exceptions\GeneralException;

class ImageUploadService
{

    protected $imageService;

    protected $fileName;

    /**
     * ImageUpload constructor.
     */
    public function __construct()
    {
        $this->imageService = new \Intervention\Image\ImageManagerStatic();
    }

    /**
     * @param $image
     * @param null $folder
     * @return string
     * @throws GeneralException
     */
    public function saveImage($image, $folder = null)
    {
        $this->fileName = null; //Reset if we store multiple images
        try {
            return $image->store('');
        } catch (\Exception $e) {
            throw new GeneralException('There has been an error with the file upload. - ' . $e->getMessage());
        }
    }

    /**
     * @param UploadedFile $image
     * @param $folder
     * @return string
     */
    protected function getPath(UploadedFile $image, $folder = null)
    {
        $filename = $this->getFilename($image, $folder);
        return public_path($filename);
    }

    /**
     * @param $image
     * @param null $folder
     * @return string
     */
    protected function getFilename($image, $folder = null)
    {
        if ($this->fileName) {
            return $this->fileName;
        }
        $f = '';
        if ($folder != null) {
            $f = DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
        }
        $this->fileName = $f. md5($image->getClientOriginalName() . microtime() ) . '.' . $image->getClientOriginalExtension();
        return $this->fileName;
    }
}

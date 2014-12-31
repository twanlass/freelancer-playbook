<?php

use Intervention\Image\Image;

class Hooks_redactor extends Hooks
{

    public function redactor__upload()
    {
        $destination = Request::get('path');
        $files = _Upload::uploadBatch($destination, 'file');
        $return = array('filelink' => $files[0]['path']);

        echo stripslashes(json_encode($return));
    }

    public function redactor__fetch_images()
    {
        if (!Auth::getCurrentMember()) {
            exit("Invalid Request");
        }

        $dir = Path::tidy(ltrim(Request::get('path'), '/').'/');
        $image_list = glob($dir."*.{jpg,jpeg,gif,png}", GLOB_BRACE);

        $images = array();
        if (count($image_list) > 0) {
            foreach ($image_list as $image) {
                $image = Path::toAsset($image);
                $images[] = array(
                    'thumb' => $image,
                    'image' => $image
                );
            }
        }

        echo json_encode($images);
    }

    public function redactor__fetch_files()
    {
        if (!Auth::getCurrentMember()) {
            exit("Invalid Request");
        }

        $dir = Path::tidy(ltrim(Request::get('path'), '/').'/');
        $file_list = glob($dir."*.*", GLOB_BRACE);

        $files = array();
        if (count($file_list) > 0) {
            foreach ($file_list as $file) {
                $pi = pathinfo($file);
                $files[] = array(
                    'link'  => Path::toAsset($file),
                    'title' => $pi['filename'],
                    'name'  => $pi['basename'],
                    'size'  => File::getHumanSize(File::getSize(Path::assemble(BASE_PATH, $file)))
                );
            }
        }

        echo json_encode($files);
    }
}

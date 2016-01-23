<?php
namespace mmedojevicbg\SmartImageField;
use yii\base\Action;
use yii\helpers\Json;

class ImageUploadAction extends Action
{
    public $uploadsPath;
    public $uploadsUrl;
    public function run()
    {
        $sourcePath = $_FILES['file']['tmp_name'];
        if(getimagesize($sourcePath)) {
            $targetPath = $this->uploadsPath . DIRECTORY_SEPARATOR . $_FILES['file']['name'];
            if(file_exists($targetPath)) {
                $info = pathinfo($targetPath);
                $targetPath = $info['dirname']
                                . DIRECTORY_SEPARATOR
                                . md5(mt_rand())
                                . '.'
                                . $info['extension'];
            }
            move_uploaded_file($sourcePath, $targetPath) ;
            $response = ['status' => 1,
                         'message' => 'Image has been uploaded successfully.',
                         'filename' => $this->generateUrl($targetPath)];
        } else {
            $response = ['status' => 0,
                         'message' => 'Uploaded file is not an image.',
                         'filename' => ''];
        }
        echo Json::encode($response);
    }
    protected function generateUrl($targetPath) {
        $info = pathinfo($targetPath);
        return $this->uploadsUrl . '/' . $info['filename'] . '.' . $info['extension'];
    }
}
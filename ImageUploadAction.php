<?php
namespace mmedojevicbg\SmartImageField;
use yii\base\Action;
use yii\helpers\Json;

class ImageUploadAction extends Action
{
    public function run()
    {
        $sourcePath = $_FILES['file']['tmp_name'];
        if(getimagesize($sourcePath)) {
            $targetPath = $this->uploadsPath = DIRECTORY_SEPARATOR . $_FILES['file']['name'];
            move_uploaded_file($sourcePath, $targetPath) ;
            $response = ['status' => 1,
                         'message' => 'Image has been uploaded successfully.'];
        } else {
            $response = ['status' => 0,
                         'message' => 'Uploaded file is not an image.'];
        }
        echo Json::encode($response);
    }
}
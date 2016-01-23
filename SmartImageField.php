<?php
namespace mmedojevicbg\SmartImageField;

use yii\helpers\Html;
use yii\widgets\InputWidget;

class SmartImageField extends InputWidget
{
    /**
     * @var SmartImageFieldAsset
     */
    protected $asset;
    protected $fileInputId;
    protected $fileInputClass;
    protected $imagePreviewId;
    protected $fieldName;
    public $uploadsHandler;
    public function init()
    {
        parent::init();
        if ($this->hasModel()) {
            $this->fieldName = $this->attribute;
        } else {
            $this->fieldName = $this->name;
        }
        $this->createImagePreviewId();
        $this->createFileInputClass();
        $this->createFileInputId();
    }
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $this->attribute, $this->options);
            $imagePath = $this->model->{$this->attribute};

        } else {
            echo Html::hiddenInput($this->name, $this->value, $this->options);
            $imagePath = $this->value;
        }
        $this->createFileInputId($this->fieldName);
        $this->registerClientScript();
        $this->renderImagePreview($imagePath);
        $this->renderFileInput();
    }
    protected function registerClientScript()
    {
        $view = $this->getView();
        $this->asset = SmartImageFieldAsset::register($view);
        $js = <<<EOT
        $('#$this->fileInputId').change(function(){
            var file = this.files[0];
            var formData=new FormData();
            formData.append("file", file);
            $.ajax({
                url: "$this->uploadsHandler",
                type: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData:false,
                success: function(data)
                {
                    data = jQuery.parseJSON(data);
                    $('#$this->imagePreviewId').attr('src', data.filename);
                }
            });
        });
EOT;
        $view->registerJs($js);
    }
    protected function renderImagePreview($imagePath)
    {
        echo Html::beginTag('div', ['class' => 'smart-image-field-container']);
        if(!$imagePath) {
            $imagePath = $this->asset->baseUrl . '/no-image.png';
        }
        echo Html::img($imagePath, ['style' => 'width: 200px; height: 200px;',
                                    'id' => $this->imagePreviewId]);
        echo Html::endTag('div');
    }
    protected function renderFileInput() {
        echo Html::fileInput($this->fileInputId, null, ['class' => $this->fileInputClass,
                                                        'id' => $this->fileInputId]);
    }
    protected function createFileInputId() {
        return $this->fileInputId = 'smart-image-field-fileinput-' . $this->fieldName;
    }
    protected function createFileInputClass() {
        return $this->fileInputClass = 'smart-image-field-fileinput';
    }
    protected function createImagePreviewId() {
        return $this->imagePreviewId = 'smart-image-field-preview-' . $this->fieldName;
    }
}
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
    public function init()
    {
        parent::init();
    }
    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $this->attribute, $this->options);
            $imagePath = $this->model->{$this->attribute};
            $this->createFileInputId($this->attribute);
        } else {
            echo Html::hiddenInput($this->name, $this->value, $this->options);
            $imagePath = $this->value;
            $this->createFileInputId($this->name);
        }
        $this->renderImagePreview($imagePath);
        $this->renderFileInput();
    }
    protected function registerClientScript()
    {
        $view = $this->getView();
        $this->asset = SmartImageFieldAsset::register($view);
    }
    protected function renderImagePreview($imagePath)
    {
        echo Html::beginTag('div', ['class' => 'smart-image-field-container']);
        if(!$imagePath) {
            $imagePath = $this->asset->baseUrl . '/no-image.png';
        }
        echo Html::img($imagePath, ['style' => 'width: 200px; height: 200px;']);
        echo Html::endTag('div');
    }
    protected function renderFileInput() {
        echo Html::fileInput($this->fileInputId);
    }
    protected function createFileInputId($fieldName) {
        $this->fileInputId = 'smart-image-field-fileinput-' . $fieldName;
    }
}
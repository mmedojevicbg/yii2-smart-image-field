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
    protected $hiddenId;
    protected $removeLinkId;
    protected $noImagePath;
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
        $this->createHiddenId();
        $this->createRemoveLinkId();
    }
    public function run()
    {
        $this->options['id'] = $this->hiddenId;
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
        $this->noImagePath = $this->asset->baseUrl . '/no-image.png';
        $js = <<<EOT
        if(!$(".sif-modal").size()) {
          $('body').append('<div class="sif-modal"></div>');
        }
        if($('#$this->hiddenId').val()) {
            $('#$this->removeLinkId').show();
        } else {
            $('#$this->removeLinkId').hide();
        }
        $('#$this->fileInputId').change(function(){
            var file = this.files[0];
            var formData=new FormData();
            formData.append("file", file);
            $('body').addClass("sif-loading");
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
                    $('#$this->hiddenId').val(data.filename);
                    $('body').removeClass("sif-loading");
                    $('#$this->removeLinkId').show();
                }
            });
        });
        $('#$this->removeLinkId').click(function(){
            $('#$this->hiddenId').val("");
            $('#$this->removeLinkId').hide();
            $('#$this->imagePreviewId').attr('src', '$this->noImagePath');
            $('#$this->fileInputId').val('');
        });
EOT;
        $view->registerJs($js);
        $css = <<<EOT
        .sif-modal {
            background: rgba( 255, 255, 255, .8 )
            url('{$this->asset->baseUrl}/ajax-loader.gif')
            50% 50%
            no-repeat;
        }
EOT;
        $view->registerCss($css);
    }
    protected function renderImagePreview($imagePath)
    {
        echo Html::beginTag('div', ['class' => 'smart-image-field-container']);
        echo Html::beginTag('div', ['class' => 'smart-image-delete',
                                    'id' => $this->removeLinkId]);
        echo Html::endTag('div');
        if(!$imagePath) {
            $imagePath = $this->noImagePath;
        }
        echo Html::img($imagePath, ['class' => 'smart-image-preview',
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
    protected function createHiddenId() {
        return $this->hiddenId = 'smart-image-field-hidden-' . $this->fieldName;
    }
    protected function createRemoveLinkId() {
        return $this->removeLinkId = 'smart-image-delete-' . $this->fieldName;
    }
}
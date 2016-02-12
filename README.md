Smart Image Field - Yii2 extension
=====

This is replacement of classic file upload HTML form element. Advantages of this field are:
* You don't have to handle file upload yourself. It is encapsulated in ImageUploadAction.
* ImageUploadAction resolves upload name clashes by default.
* If existing model is edited, image field is pre-populated.
* Uploaded image won't be lost if form has validation errors after submit.
* There is a delete image link besides upload button.

Installation
---

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mmedojevicbg/yii2-smart-image-field "dev-master"
```

or add

```
"mmedojevicbg/yii2-smart-image-field": "dev-master"
```

to the require section of your `composer.json` file.

Usage
---

1) Attach ImageUploadAction to designated controller

```php
class SiteController extends BaseController
{
    function actions()
    {
        return [
            'upload' => [
                'class' => 'mmedojevicbg\SmartImageField\ImageUploadAction',
                'uploadsPath' => \Yii::getAlias('@webroot') . '/uploads',
                'uploadsUrl' => '/uploads'
            ]
        ];
    }
}
```

2) Utilize SmartImageField inside ActiveForm

```php
echo SmartImageField::widget(['model' => $model,
                              'attribute' => 'profile_image',
                              'uploadsHandler' => '/site/upload']);
```

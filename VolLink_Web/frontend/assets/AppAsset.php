<?php

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Recursos do front-office: HTML e CSS próprios, sem frameworks de interface.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        'css/site.css',
    ];
    public $js = [
        'js/site.js',
    ];
    public $depends = [
        YiiAsset::class,
    ];
}

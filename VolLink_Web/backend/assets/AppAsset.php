<?php

declare(strict_types=1);

namespace backend\assets;

use hail812\adminlte3\assets\AdminLteAsset;
use hail812\adminlte3\assets\FontAwesomeAsset;
use yii\web\AssetBundle;

/**
 * Recursos do back-office: AdminLTE 3 (hail812/yii2-adminlte3) mais o estilo do VolLink.
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
        AdminLteAsset::class,
        FontAwesomeAsset::class,
    ];
}

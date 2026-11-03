<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array<string, string> $company */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Editar perfil público';
?>
<div class="container page">
    <div class="page-narrow" style="max-width: 920px">
        <div class="page-head">
            <div>
                <h1>Editar perfil público</h1>
                <p>Atualiza a informação visível para os voluntários.</p>
            </div>
        </div>

        <?php /* TODO(feat/company): gravar o perfil público em user_details (RF-FO-20). */ ?>
        <form class="card form-card" style="padding-bottom: 0" onsubmit="return false">
            <div class="field">
                <label for="company-name">Nome da organização</label>
                <input id="company-name" class="input" type="text" value="<?= Html::encode($company['name']) ?>">
            </div>
            <div class="field">
                <label for="company-about">Descrição</label>
                <textarea id="company-about" class="input" rows="5" placeholder="Descreve a organização"></textarea>
            </div>
            <div class="field">
                <label for="company-website">Website</label>
                <input id="company-website" class="input" type="url" placeholder="https://horizonte.pt">
            </div>
            <div class="field">
                <label for="company-location">Localização</label>
                <input id="company-location" class="input" type="text" placeholder="Leiria">
            </div>

            <div class="form-foot" style="justify-content: flex-end; gap: 10px">
                <a class="btn" href="<?= Url::to(['/company/profile']) ?>">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar alterações</button>
            </div>
        </form>
    </div>
</div>

<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array<string, mixed> $volunteer */

use common\helpers\Ui;
use yii\helpers\Html;

$this->title = 'Perfil e histórico';
?>
<div class="container page">
    <div class="page-head">
        <div>
            <h1>Perfil e histórico</h1>
            <p>Consulta os teus dados e impacto.</p>
        </div>
    </div>

    <div class="grid-2">
        <section class="card profile-card">
            <?= Ui::avatar($volunteer['name'], 'xl') ?>
            <h2><?= Html::encode($volunteer['name']) ?></h2>
            <p class="muted"><?= Html::encode($volunteer['email']) ?></p>

            <div class="kv">
                <small>Competências</small>
                <?= Html::encode($volunteer['skills']) ?>
            </div>
            <div class="kv">
                <small>Data de nascimento</small>
                <?= Html::encode($volunteer['birth']) ?>
            </div>

            <?php /* TODO(feat/auth): formulário de edição do perfil (RF-FO-05). */ ?>
            <button class="btn" type="button">Editar perfil</button>
        </section>

        <section class="card history" id="historico">
            <div class="history-head">
                <div>
                    <strong>Histórico</strong>
                    <small>Total de voluntariado</small>
                </div>
                <span class="total"><?= Html::encode($volunteer['total']) ?></span>
            </div>

            <?php foreach ($volunteer['history'] as $entry): ?>
                <div class="history-row">
                    <span class="dot"></span>
                    <div>
                        <strong><?= Html::encode($entry['event']) ?></strong>
                        <small><?= Html::encode($entry['meta']) ?></small>
                    </div>
                    <span class="hours"><?= Html::encode($entry['hours']) ?></span>
                </div>
            <?php endforeach ?>
        </section>
    </div>

    <section class="card security">
        <h2>Segurança</h2>
        <?php /* TODO(feat/auth): alterar a palavra-passe com confirmação da atual (RF-FO-12). */ ?>
        <form class="security-grid" onsubmit="return false">
            <div class="field">
                <label for="password-current">Palavra-passe atual</label>
                <input id="password-current" class="input" type="password" placeholder="Palavra-passe atual" autocomplete="current-password">
            </div>
            <div class="field">
                <label for="password-new">Nova palavra-passe</label>
                <input id="password-new" class="input" type="password" placeholder="Nova palavra-passe" autocomplete="new-password">
            </div>
            <div class="field">
                <label for="password-repeat">Confirmar palavra-passe</label>
                <input id="password-repeat" class="input" type="password" placeholder="Confirmar" autocomplete="new-password">
            </div>
            <button class="btn btn-primary" type="submit">Guardar</button>
        </form>
    </section>
</div>

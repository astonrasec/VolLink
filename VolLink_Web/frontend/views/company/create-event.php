<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var list<string> $categories */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Criar evento';

/*
 * Passo 1 de 3 do assistente. Os passos "Turnos" e "Publicar" e a gravação do rascunho
 * (RF-FO-02 e RF-FO-07) ficam para a funcionalidade de eventos (feat/events).
 */
?>
<div class="container page">
    <div class="page-narrow" style="max-width: 800px">
        <div class="page-head" style="margin-bottom: 0">
            <div>
                <h1>Criar evento</h1>
                <p>Preenche os detalhes e publica quando estiver pronto.</p>
            </div>
        </div>

        <ol class="stepper" style="padding: 0" aria-label="Passos">
            <li class="step active"><span class="n">1</span> Detalhes</li>
            <li class="step"><span class="n">2</span> Turnos</li>
            <li class="step"><span class="n">3</span> Publicar</li>
        </ol>

        <form class="card form-card" onsubmit="return false">
            <div class="field">
                <label for="event-title">Título</label>
                <input id="event-title" class="input" type="text" placeholder="Nome do evento">
            </div>
            <div class="field">
                <label for="event-description">Descrição</label>
                <textarea id="event-description" class="input" rows="4" placeholder="Descreve o objetivo e as atividades"></textarea>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="event-category">Categoria</label>
                    <select id="event-category" class="input">
                        <?php foreach ($categories as $category): ?>
                            <option><?= Html::encode($category) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="field">
                    <label for="event-location">Localização</label>
                    <input id="event-location" class="input" type="text" placeholder="Local do evento">
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="event-start">Início</label>
                    <input id="event-start" class="input" type="datetime-local">
                </div>
                <div class="field">
                    <label for="event-end">Fim</label>
                    <input id="event-end" class="input" type="datetime-local">
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="event-deadline">Prazo de candidatura</label>
                    <input id="event-deadline" class="input" type="date">
                </div>
                <div class="field">
                    <label for="event-capacity">Capacidade</label>
                    <input id="event-capacity" class="input" type="number" min="1" placeholder="50">
                </div>
            </div>
            <div class="field">
                <label for="event-skills">Competências necessárias</label>
                <input id="event-skills" class="input" type="text" placeholder="Ex.: Comunicação, Primeiros socorros">
            </div>

            <div class="form-foot">
                <a class="btn" href="<?= Url::to(['/company/events']) ?>">Voltar</a>
                <button class="btn btn-primary" type="button">Continuar</button>
            </div>
        </form>
    </div>
</div>

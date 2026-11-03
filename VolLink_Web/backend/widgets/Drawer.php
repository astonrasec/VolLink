<?php

declare(strict_types=1);

namespace backend\widgets;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * Painel de detalhe que abre pelo lado direito (modal do Bootstrap 4 usado pelo AdminLTE).
 *
 * ```php
 * Drawer::begin(['id' => 'user-1', 'title' => 'Ana Silva', 'footer' => $buttons]);
 * echo '<p>Conteúdo do painel</p>';
 * Drawer::end();
 * ```
 *
 * O painel abre com `data-toggle="modal" data-target="#user-1"`.
 */
final class Drawer extends Widget
{
    public string $title = '';
    public string $subtitle = '';
    /** HTML da pílula de estado mostrada ao lado do título. */
    public string $pill = '';
    /** HTML do avatar mostrado antes do título. */
    public string $avatar = '';
    /** HTML dos botões do rodapé. */
    public string $footer = '';

    public function init(): void
    {
        parent::init();
        ob_start();
        ob_implicit_flush(false);
    }

    public function run(): string
    {
        $body = (string) ob_get_clean();
        $id = $this->getId();
        $titleId = $id . '-title';

        $heading = Html::tag('h2', Html::encode($this->title) . $this->pill, ['id' => $titleId]);
        if ($this->subtitle !== '') {
            $heading .= Html::tag('p', Html::encode($this->subtitle));
        }

        $close = Html::button(
            Html::tag('span', '&times;', ['aria-hidden' => 'true']),
            ['type' => 'button', 'class' => 'close', 'data-dismiss' => 'modal', 'aria-label' => 'Fechar'],
        );

        $head = Html::tag('div', $this->avatar . Html::tag('div', $heading, ['class' => 'vl-drawer-heading']) . $close, ['class' => 'vl-drawer-head']);
        $main = Html::tag('div', $body, ['class' => 'vl-drawer-body']);
        $foot = $this->footer === '' ? '' : Html::tag('div', $this->footer, ['class' => 'vl-drawer-foot']);

        $content = Html::tag('div', $head . $main . $foot, ['class' => 'modal-content']);
        $dialog = Html::tag('div', $content, ['class' => 'modal-dialog', 'role' => 'document']);

        return Html::tag('div', $dialog, [
            'id' => $id,
            'class' => 'modal fade vl-drawer',
            'tabindex' => '-1',
            'role' => 'dialog',
            'aria-labelledby' => $titleId,
            'aria-hidden' => 'true',
        ]);
    }
}

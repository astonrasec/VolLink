<?php

declare(strict_types=1);

namespace common\helpers;

use yii\helpers\Html;

/**
 * Pequenos componentes visuais partilhados pelo front-office e pelo back-office:
 * etiquetas de estado (pílulas) e avatares com iniciais.
 */
final class Ui
{
    /** Rótulo de estado (em minúsculas) -> tom visual da pílula. */
    private const TONES = [
        'ativo' => 'green',
        'publicado' => 'green',
        'aprovada' => 'green',
        'approved' => 'green',
        'verificada' => 'green',
        'pendente' => 'amber',
        'pending' => 'amber',
        'suspenso' => 'red',
        'suspended' => 'red',
        'rejeitada' => 'red',
        'rejected' => 'red',
        'cancelado' => 'red',
        'cancelada' => 'red',
        'concluído' => 'blue',
        'concluido' => 'blue',
        'rascunho' => 'gray',
        'inativo' => 'gray',
        'arquivado' => 'gray',
    ];

    /**
     * Devolve o tom visual (green, amber, red, blue ou gray) de um estado.
     */
    public static function tone(string $label): string
    {
        return self::TONES[mb_strtolower($label)] ?? 'gray';
    }

    /**
     * Pílula de estado, por exemplo "Publicado" ou "PENDING".
     */
    public static function pill(string $label, ?string $tone = null): string
    {
        $tone ??= self::tone($label);

        return Html::tag(
            'span',
            Html::tag('i', '', ['class' => 'pill-dot']) . Html::encode($label),
            ['class' => 'pill pill-' . $tone],
        );
    }

    /**
     * Iniciais de um nome: as primeiras letras das duas primeiras palavras.
     */
    public static function initials(string $name): string
    {
        $words = preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $letters = array_map(static fn (string $word): string => mb_substr($word, 0, 1), array_slice($words, 0, 2));

        return mb_strtoupper(implode('', $letters));
    }

    /**
     * Avatar redondo com as iniciais do nome. O tamanho "lg" é usado nos painéis de detalhe.
     */
    public static function avatar(string $name, string $size = 'md'): string
    {
        return Html::tag('span', Html::encode(self::initials($name)), [
            'class' => 'avatar avatar-' . $size,
            'aria-hidden' => 'true',
        ]);
    }
}

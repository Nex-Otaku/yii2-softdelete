<?php

namespace nex_otaku\softdelete\behaviors;

use yii2tech\ar\softdelete\SoftDeleteBehavior as BaseSoftDeleteBehavior;

/**
 * Настраиваем SoftDeleteBehavior на использование поля "deleted".
 *
 * @author Nex Otaku <nex@otaku.ru>
 */
class SoftDeleteBehavior extends BaseSoftDeleteBehavior
{
    public $softDeleteAttributeValues = [
        'deleted' => true
    ];
}

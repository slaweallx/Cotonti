<?php

namespace cot\controllers;

class BaseController
{
    /**
     * @var ?string the ID of the action that is used when the action ID is not specified
     * in the request. Defaults to 'index'.
     */
    public static $defaultAction = 'index';

    /**
     * Declares external actions for the controller.
     *
     * This method can be overridden to declare external actions.
     * The method returns an array, the keys of which are the IDs of the actions, and the values are the corresponding
     * names of the action classes. For example:
     * ```php
     * return [
     *   'action1' => 'cot\modules\someext\controllers\Action1',
     *   'action2' => [
     *     'class' => 'cot\modules\someext\controllers\Action1',
     *     'property1' => 'value1',
     *     'property2' => 'value2',
     *   ],
     * ];
     * ```
     * @return array<string, class-string|array<string, class-string|string>>
     */
    public static function actions(): array
    {
        return [];
    }
}
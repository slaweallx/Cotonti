<?php

namespace cot\router;

use cot\controllers\BaseController;

class Route
{
    /**
     * @var BaseController
     */
    public $controller;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $extensionType;

    /**
     * @var string
     */
    public $extensionCode;
}
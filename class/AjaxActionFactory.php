<?php

namespace Avorg;

use function defined;
use natlib\Factory;
use ReflectionException;

if (!defined('ABSPATH')) exit;

class AjaxActionFactory
{
    /** @var ScanningFactory $scanningFactory */
    private $scanningFactory;

    public function __construct(ScanningFactory $scanningFactory)
    {
        $this->scanningFactory = $scanningFactory;
    }

    public function registerCallbacks()
    {
        $entity = $this->scanningFactory->getEntities("class/AjaxAction");
        array_walk($entity, function (AjaxAction $entity) {
            $entity->registerCallbacks();
        });
    }
}
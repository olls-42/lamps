<?php

namespace App\EntityManager\Observer;

interface ObservableEntityInterface
{
    /**
     * @deprecated
     * @return array
     */
    public function getObserversConfiguration(): array;
}

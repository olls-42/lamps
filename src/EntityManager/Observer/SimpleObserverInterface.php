<?php

namespace App\EntityManager\Observer;

use SplObserver;
use SplSubject;

/**
 * @inheritdoc
 */
interface SimpleObserverInterface extends SplObserver
{
    /**
     * @inheritdoc
     *
     * @param SplSubject|SimpleSubjectInterface $subject
     * @return void
     */
    public function update(SplSubject|SimpleSubjectInterface $subject): void;
}

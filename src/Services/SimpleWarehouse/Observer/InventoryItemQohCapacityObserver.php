<?php

namespace App\Services\SimpleWarehouse\Observer;

use App\Entity\InventoryItem;
use App\EntityManager\Observer\SimpleSubjectInterface;
use App\EntityManager\Observer\SimpleObserverInterface;
use SplSubject;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Service\Attribute\Required;

class InventoryItemQohCapacityObserver implements SimpleObserverInterface
{
    private string $description = '';
    private MailerInterface $mailer;

    #[Required]
    public function setMailer(MailerInterface $mailer): void
    {
        $this->mailer = $mailer;
    }

    public function setDescription(string $description = ''): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function update(SimpleSubjectInterface|SplSubject $subject): void
    {
        // todo fix type hint by correct return
        /** @var InventoryItem $inventoryItem */
        $inventoryItem = $subject->getObservableEntity();

        if ($inventoryItem->getQoh()->value() < 5) {

            $str =  "sku: {$inventoryItem->getSku()->value()}, has low qoh: {$inventoryItem->getQoh()->value()}";

            $email = (new Email())
                ->from('hello@example.com')
                ->to('you@example.com')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject($str)
                ->text($str)
                ->html($str);

            $this->mailer->send($email);
        }
    }
}

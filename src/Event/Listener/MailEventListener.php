<?php

namespace App\Event\Listener;

use App\Entity\User;
use Doctrine\ORM\Event\PostPersistEventArgs;

class MailEventListener
{
// the listener methods receive an argument which gives you access to
    // both the entity object of the event and the entity manager itself
    public function postPersist(User $user, PostPersistEventArgs $event): void
    {
        dd(1);
    }
}
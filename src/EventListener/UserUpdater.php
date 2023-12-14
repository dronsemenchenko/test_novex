<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;

use App\Entity\User;

class UserUpdater
{

    public function postUpdate(User $User, LifecycleEventArgs $event): void
    {
        $d2 = new \DateTime();
        $diff = $d2->diff($User->getBirthday());
        $User->setAge($diff->y);
    }
}

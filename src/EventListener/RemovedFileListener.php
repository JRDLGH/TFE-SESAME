<?php
/**
 * Created by PhpStorm.
 * User: JRDN
 * Date: 21/05/2018
 * Time: 13:17
 */

namespace App\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Vich\UploaderBundle\Event\Event;


class RemovedFileListener
{
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function onPostRemove(Event $evt)
    {
        $file = $evt->getObject();

        try{
            $this->manager->persist($file);
            $this->manager->flush();
        }catch(ORMException $e){
            print($e->getMessage());
        }

    }
}
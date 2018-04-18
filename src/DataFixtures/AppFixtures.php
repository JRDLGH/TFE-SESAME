<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Thesaurus\Gesture;
use App\Entity\Thesaurus\Gesture\Tag;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        /**
         * USER
         */
        // ADMIN
        $user = new User();
        $user->setFirstname('Jordan');
        $user->setLastname('LGH');
        $user->setEmail('admin@sesame.be');
        $user->setPassword('admin');
        $user->setUsername('admin');
        $user->setEnabled(true);
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        //STANDARD USER
        $user = new User();
        $user->setFirstname('Jordan');
        $user->setLastname('lagache');
        $user->setEmail('user@sesame.be');
        $user->setPassword('user');
        $user->setUsername('user');
        $user->setEnabled(true);
        $manager->persist($user);
        /**
         * GESTURES
         */

        //Creating 'AUTO' gesture
        $gesture = new Gesture();
        $gesture->setName('auto');
        $gesture->setDescription('Moyen de locomotion.');
        $gesture->setIsPublished(true);

        $tag = new Tag();
        $tag->setKeyword('Conduire');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('Vehicule');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag->setKeyword('Automobile');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag->setKeyword('Voiture');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $manager->persist($gesture);

        //Creating 'AVION' gesture
        $gesture = new Gesture();
        $gesture->setName('Avion');
        $gesture->setDescription('Permet de voler.');
        $gesture->setIsPublished(true);

        $tag = new Tag();
        $tag->setKeyword('Conduire');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('piloter');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('Avion');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $manager->persist($gesture);

        //Creating 'AVOIR MAL' gesture
        $gesture = new Gesture();
        $gesture->setName('Avoir mal');
        $gesture->setDescription('S\'être fait mal, blessé.');
        $gesture->setIsPublished(true);

        $tag = new Tag();
        $tag->setKeyword('blessé');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('avoir');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('mal');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $manager->persist($gesture);

        //Creating 'AVOIR MAL A LA TETE' gesture
        $gesture = new Gesture();
        $gesture->setName('Avoir mal à la tête');
        $gesture->setDescription('S\'être fait mal à la tête, blessé.');
        $gesture->setIsPublished(true);

        $tag = new Tag();
        $tag->setKeyword('blessé');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('avoir');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('mal');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('maux');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('tête');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $manager->persist($gesture);

        //Creating 'AVOIR MAL AU VENTRE' gesture
        $gesture = new Gesture();
        $gesture->setName('Avoir mal au ventre');
        $gesture->setDescription('Avoir des douleurs au ventre.');
        $gesture->setIsPublished(true);

        $tag = new Tag();
        $tag->setKeyword('malade');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('avoir');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('douleur');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('mal');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('ventre');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('maux');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('estomac');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $manager->persist($gesture);

        //Creating 'AVOIR PERDU' gesture
        $gesture = new Gesture();
        $gesture->setName('Avoir perdu (au jeu)');
        $gesture->setDescription('S\'être battre par quelqu\'un d\'autre mal à un jeu.');
        $gesture->setIsPublished(true);

        $tag = new Tag();
        $tag->setKeyword('perdre');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('avoir');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $tag = new Tag();
        $tag->setKeyword('jeu');
        $manager->persist($tag);
        $gesture->addTag($tag);

        $manager->persist($gesture);

        $manager->flush();

    }
}

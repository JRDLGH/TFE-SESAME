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
         * TAGS
         */
        //conduire
        $conduireTag = new Tag();
        $conduireTag->setKeyword('Conduire');
        $manager->persist($conduireTag);

        //vehicule
        $vehiculeTag = new Tag();
        $vehiculeTag->setKeyword('Vehicule');
        $manager->persist($vehiculeTag);

        //automobile
        $automobileTag = new Tag();
        $automobileTag->setKeyword('Automobile');
        $manager->persist($automobileTag);

        //voiture
        $voitureTag = new Tag();
        $voitureTag->setKeyword('Voiture');
        $manager->persist($voitureTag);

        //piloter
        $piloterTag = new Tag();
        $piloterTag->setKeyword('piloter');
        $manager->persist($piloterTag);

        //Avion
        $avionTag = new Tag();
        $avionTag->setKeyword('Avion');
        $manager->persist($avionTag);

        //blessé
        $blesseTag = new Tag();
        $blesseTag->setKeyword('blessé');
        $manager->persist($blesseTag);

        //avoir
        $avoirTag = new Tag();
        $avoirTag->setKeyword('avoir');
        $manager->persist($avoirTag);

        //mal
        $malTag = new Tag();
        $malTag->setKeyword('mal');
        $manager->persist($malTag);

        //maux
        $mauxTag = new Tag();
        $mauxTag->setKeyword('maux');
        $manager->persist($mauxTag);

        //tete
        $teteTag = new Tag();
        $teteTag->setKeyword('tête');
        $manager->persist($teteTag);

        //malade
        $maladeTag = new Tag();
        $maladeTag->setKeyword('malade');
        $manager->persist($maladeTag);

        //douleur
        $douleurTag = new Tag();
        $douleurTag->setKeyword('douleur');
        $manager->persist($douleurTag);

        //ventre
        $ventreTag = new Tag();
        $ventreTag->setKeyword('ventre');
        $manager->persist($ventreTag);

        //estomac
        $estomacTag = new Tag();
        $estomacTag->setKeyword('estomac');
        $manager->persist($estomacTag);

        //perdre
        $perdreTag = new Tag();
        $perdreTag->setKeyword('perdre');
        $manager->persist($perdreTag);

        //jeu
        $jeuTag = new Tag();
        $jeuTag->setKeyword('jeu');
        $manager->persist($jeuTag);
        /**
         * GESTURES
         */

        //Create 'Avoir une voiture' gesture
        $gesture = new Gesture();
        $gesture->setName('Avoir une voiture');
        $gesture->setDescription('Posséder une voiture.');
        $gesture->setIsPublished(true);

        $gesture->addTag($avoirTag);
        $gesture->addTag($vehiculeTag);
        $gesture->addTag($voitureTag);
        $gesture->addTag($conduireTag);

        $manager->persist($gesture);

        //Creating 'AUTO' gesture
        $gesture = new Gesture();
        $gesture->setName('auto');
        $gesture->setDescription('Moyen de locomotion.');
        $gesture->setIsPublished(true);

        $gesture->addTag($conduireTag);
        $gesture->addTag($vehiculeTag);
        $gesture->addTag($automobileTag);
        $gesture->addTag($voitureTag);

        $manager->persist($gesture);

        //Create 'Avoir' gesture
        $gesture = new Gesture();
        $gesture->setName('Avoir');
        $gesture->setDescription('Verbe auxilière avoir.');
        $gesture->setIsPublished(true);

        $gesture->addTag($avoirTag);

        $manager->persist($gesture);

        //Creating 'AVOIR MAL' gesture
        $gesture = new Gesture();
        $gesture->setName('Avoir mal');
        $gesture->setDescription('S\'être fait mal, blessé.');
        $gesture->setIsPublished(true);


        $gesture->addTag($blesseTag);
        $gesture->addTag($avoirTag);
        $gesture->addTag($malTag);

        $manager->persist($gesture);

        //Creating 'AVOIR MAL A LA TETE' gesture
        $gesture = new Gesture();
        $gesture->setName('Avoir mal à la tête');
        $gesture->setDescription('S\'être fait mal à la tête, blessé.');
        $gesture->setIsPublished(true);

        $gesture->addTag($blesseTag);
        $gesture->addTag($avoirTag);
        $gesture->addTag($malTag);
        $gesture->addTag($mauxTag);
        $gesture->addTag($teteTag);

        $manager->persist($gesture);

        //Creating 'AVOIR MAL AU VENTRE' gesture
        $gesture = new Gesture();
        $gesture->setName('Avoir mal au ventre');
        $gesture->setDescription('Avoir des douleurs au ventre.');
        $gesture->setIsPublished(true);


        $gesture->addTag($maladeTag);
        $gesture->addTag($avoirTag);
        $gesture->addTag($douleurTag);
        $gesture->addTag($malTag);
        $gesture->addTag($ventreTag);
        $gesture->addTag($mauxTag);
        $gesture->addTag($estomacTag);

        $manager->persist($gesture);

        //Creating 'AVION' gesture
        $gesture = new Gesture();
        $gesture->setName('Avion');
        $gesture->setDescription('Permet de voler.');
        $gesture->setIsPublished(true);

        $gesture->addTag($conduireTag);
        $gesture->addTag($piloterTag);
        $gesture->addTag($avionTag);

        $manager->persist($gesture);

        //Create 'Avoir' gesture
        $gesture = new Gesture();
        $gesture->setName('Avoir tout');
        $gesture->setDescription('Verbe auxilière avoir.');
        $gesture->setIsPublished(true);

        //not tagged
        $manager->persist($gesture);

        //Creating 'AVOIR PERDU' gesture
        $gesture = new Gesture();
        $gesture->setName('Avoir perdu (au jeu)');
        $gesture->setDescription('S\'être battre par quelqu\'un d\'autre mal à un jeu.');
        $gesture->setIsPublished(true);


        $gesture->addTag($jeuTag);
        $gesture->addTag($avoirTag);
        $gesture->addTag($perdreTag);

        $manager->persist($gesture);

        //SEND TO DB
        $manager->flush();

    }
}

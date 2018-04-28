<?php

namespace App\Tests\Form\DataTransformer;
use App\Form\DataTransformer\TagsTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use App\Entity\Thesaurus\Gesture\Tag;

class TagsTransformerTest extends TestCase{

    public function testCreateTagsArrayFromString(){
        $transfomer = $this->getMockedTransformer();
        $tags = $transfomer->reverseTransform('manger,boire,jambe');
        $this->assertCount(3,$tags);
        $this->assertSame('manger',$tags[0]->getKeyword());
    }

    public function testAlreadyCreatedTag(){
        $tag = new Tag();
        $tag->setKeyword('Avoir');
        $transformer = $this->getMockedTransformer([$tag]);
        $tags = $transformer->reverseTransform('Cool,     AvOIR,Faim');


        $this->assertCount(3,$tags);
        $this->assertSame($tag,$tags[0]);
    }

    public function testRemoveEmptyTag(){
        $transfomer = $this->getMockedTransformer();
        $tags = $transfomer->reverseTransform('manger,,boire, ,jambe');
        $this->assertCount(3,$tags);
        $this->assertSame('manger',$tags[0]->getKeyword());
    }

    public function testRemoveDuplicatedTags(){
        $transfomer = $this->getMockedTransformer();
        $tags = $transfomer->reverseTransform('Demo,Demo, demo ,Demo,DEMO,demO');
        $this->assertCount(1,$tags);
        $this->assertSame('demo',$tags[0]->getKeyword());
    }

    public function testTagsToString(){
        $transfomer = $this->getMockedTransformer();
        $keywords=['Avéôir','maNger','faim'];
        $tagsSet = [];

        //Create an array of tags
        for( $i = 0 ; $i < count($keywords) ; $i++ ){
            $tag = new Tag();
            $tag->setKeyword($keywords[$i]);
            $tagsSet[] = $tag;
        }

        $stringTags = $transfomer->transform($tagsSet);
        $this->assertSame('Avéôir,maNger,faim',$stringTags);
    }

    private function getMockedTransformer($defaultTags = [])
    {
        //Le mock permet de ne plus dépendre de la base de données.
        $tagRepository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tagRepository->expects($this->any())
            ->method('findBy')
            ->will($this->returnValue($defaultTags));

        $em = $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($tagRepository));

        return new TagsTransformer($em);
    }
}
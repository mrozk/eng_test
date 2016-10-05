<?php

use app\models\Dictionary;
use Codeception\Test\Unit;
use yii\helpers\ArrayHelper;

class DictionaryTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;


    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     *
     */
    protected function _before()
    {
        $this->dictionary = new Dictionary();
    }

    protected function _after()
    {
    }


    public function testGetItems()
    {
        $items = $this->dictionary->getItems();
        $this->assertNotEmpty($items);
        $this->assertNotEmpty($this->dictionary->getTestQueue());


    }

    public function testVariantValid()
    {
        $queue = $this->dictionary->getTestQueue();
        // Проверка правильности генерации варианта
        $id = array_pop($queue);
        $variants = $this->dictionary->getVariants($id);
        $var1 = ArrayHelper::getValue(ArrayHelper::getValue($this->dictionary->getItems(), $id), 'en');
        $var2 = ArrayHelper::getValue($variants, 'value');
        $this->assertEquals($var1, $var2);
    }

}
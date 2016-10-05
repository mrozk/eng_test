<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 03.10.16
 * Time: 21:07
 */

namespace app\models;


use yii\base\Model;
use yii\helpers\ArrayHelper;

class Dictionary extends Model
{

    public function getVariants($variantId, $variantCount = 4)
    {
        $result = [];
        $items = $this->getItems();
        $keys = array_keys($items);
        $rightRecord = ArrayHelper::getValue($items, $variantId);
        $result[] = ArrayHelper::getValue($rightRecord, 'ru');
        $variantValue = ArrayHelper::getValue($rightRecord, 'en');

        shuffle($keys);

        while (count($result) < $variantCount) {
            $next = array_pop($keys);
            if ($next != $variantId) {
                $record = ArrayHelper::getValue($items, $next);
                $result[] = ArrayHelper::getValue($record, 'ru');
            }
        }


        $pos = rand(0, $variantCount - 1);
        if($pos != 0){
            $temp = $result[$pos];
            $result[$pos] = $result[0];
            $result[0] = $temp;
        }

        return [
            'elements' => $result,
            'right' => $pos,
            'value' => $variantValue
        ];
    }

    public  function getTestQueue(){
        $items = $this->getItems();
        $keys = array_keys($items);
        shuffle($keys);

        return $keys;
    }


    public function getItems()
    {
        return [
            1 => [
                'en' => 'apple',
                'ru' => 'яблоко'
            ],
            2 => [
                'en' => 'pear',
                'ru' => 'персик'
            ],
            3 => [
                'en' => 'orange',
                'ru' => 'апельсин'
            ],
            4 => [
                'en' => 'grape',
                'ru' => 'виноград'
            ],
            5 => [
                'en' => 'lemon',
                'ru' => 'лимон'
            ],
            6 => [
                'en' => 'pineapple',
                'ru' => 'ананас'
            ],
            7 => [
                'en' => 'watermelon',
                'ru' => 'арбуз'
            ],
            8 => [
                'en' => 'coconut',
                'ru' => 'кокос'
            ],
            9 => [
                'en' => 'banana',
                'ru' => 'банан'
            ],
            10 => [
                'en' => 'pomelo',
                'ru' => 'помело'
            ],
            11 => [
                'en' => 'strawberry',
                'ru' => 'клубника'
            ],
            12 => [
                'en' => 'raspberry',
                'ru' => 'малина'
            ],
            13 => [
                'en' => 'melon',
                'ru' => 'дыня'
            ],
            14 => [
                'en' => 'apricot',
                'ru' => 'абрикос'
            ],
            15 => [
                'en' => 'mango',
                'ru' => 'манго'
            ],
            16 => [
                'en' => 'pear',
                'ru' => 'слива'
            ],
            17 => [
                'en' => 'pomegranate',
                'ru' => 'гранат'
            ],
            18 => [
                'en' => 'cherry',
                'ru' => 'вишня'
            ],
        ];
    }

    public function getInitValues($name)
    {
        if(empty($name)){
            return false;
        }

        $test = [];
        $test['queue'] = $this->getTestQueue();
        $test['user'] = $name;
        $test['score'] = 0;
        $test['errors'] = 0;

        return $test;
    }

    public function checkData($data, $variant)
    {

    }

    public function getNextVariant($data)
    {
        $queue = ArrayHelper::getValue($data, 'queue');
        if(!count($queue)){
            return false;
        }

        $next = array_pop($queue);
        $variants = $this->getVariants($next);
        $data['queue'] = $queue;
        $data['right'] = ArrayHelper::getValue($variants, 'right');
        $data['current'] = $next;

        ArrayHelper::remove($variants, 'right');

        return[
            'data' => $data,
            'result' => $variants
        ];

    }

    public function checkErrors()
    {
        return true;
    }
}
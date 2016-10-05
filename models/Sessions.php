<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sessions".
 *
 * @property integer $id
 * @property string $name
 * @property integer $success
 * @property integer $errors
 */
class Sessions extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sessions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'success', 'errors'], 'required'],
            [['success', 'errors'], 'integer'],
            [['name'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'success' => 'Success',
            'errors' => 'Errors',
        ];
    }
}

<?php

use yii\db\Migration;

/**
 * Handles the creation for table `errors`.
 */
class m161005_085955_create_errors_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $command = $this->db->createCommand('CREATE TABLE IF NOT EXISTS `errors` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `question_id` int(11) NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;');
        $command->execute();
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('errors');
    }
}

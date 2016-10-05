<?php

use yii\db\Migration;

/**
 * Handles the creation for table `sessions`.
 */
class m161005_090601_create_sessions_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $command = $this->db->createCommand('CREATE TABLE IF NOT EXISTS `sessions` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `name` varchar(256) COLLATE utf8_bin NOT NULL,
                      `success` int(11) NOT NULL,
                      `errors` int(11) NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;');
        $command->execute();
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('sessions');
    }
}

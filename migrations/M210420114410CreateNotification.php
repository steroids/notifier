<?php

namespace steroids\notifier\migrations;

use steroids\core\base\Migration;

class M210420114410CreateNotification extends Migration
{
    public function safeUp()
    {
        $this->createTable('notifier_notifications', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer(),
            'templateName' => $this->string(),
            'refId' => $this->integer(),
            'content' => $this->text(),
            'paramsJson' => $this->text(),
            'isRead' => $this->boolean()->notNull()->defaultValue(false),
            'createTime' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('notifier_notifications');
    }
}

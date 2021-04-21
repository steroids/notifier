<?php

namespace steroids\notifier\forms;

use steroids\notifier\forms\meta\NotificationSearchFormMeta;

class NotificationSearchForm extends NotificationSearchFormMeta
{
    public function prepare($query)
    {
        parent::prepare($query);

        $query->andWhere(['userId' => $this->userId]);
    }
}

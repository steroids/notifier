<?php

namespace steroids\notifier\forms;

use steroids\notifier\forms\meta\NotificationSearchFormMeta;

class NotificationSearchForm extends NotificationSearchFormMeta
{
    public function rules()
    {
        return [
            ...parent::rules(),
            ['!userId', 'safe'],
        ];
    }

    public function prepare($query)
    {
        parent::prepare($query);

        $query
            ->andWhere(['userId' => $this->userId])
            ->orderBy(['id' => SORT_DESC]);

        if ($this->skipRead) {
            $query->andWhere(['isRead' => false]);
        }
    }
}

<?php

namespace OAuthServer\Model\Bridge\Entity;

use Cake\I18n\FrozenTime;
use DateTimeImmutable;

trait ExpiryDateTimeTrait
{
    /**
     * @inheritDoc
     */
    public function getExpiryDateTime()
    {
        return FrozenTime::createFromTimestamp($this->expires);
    }

    /**
     * @inheritDoc
     */
    public function setExpiryDateTime(DateTimeImmutable $dateTime)
    {
        $this->expires = $dateTime->getTimestamp();
    }
}

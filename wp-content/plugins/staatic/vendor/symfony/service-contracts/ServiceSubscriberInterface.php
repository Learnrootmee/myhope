<?php

namespace Staatic\Vendor\Symfony\Contracts\Service;

interface ServiceSubscriberInterface
{
    public static function getSubscribedServices() : array;
}

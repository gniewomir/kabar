<?php

namespace kabar\Interfaces;

interface Cache extends \SplObserver
{
    public function isCached(\kabar\Interfaces\Cacheable $object);

    public function getCached(\kabar\Interfaces\Cacheable $object);
}

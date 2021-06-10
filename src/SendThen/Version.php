<?php


namespace SendThen;

class Version
{
    const DEFAULT_API_VERSION = 'v1';
    const MAJOR = 0;
    const MINOR = 0;
    const PATCH = 0;

    public function __toString()
    {
        return implode('.', [self::MAJOR, self::MINOR, self::PATCH]);
    }
}
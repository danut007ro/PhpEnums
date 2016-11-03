<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Unit\EnumTest;

use Elao\Enum\ReadableEnum;

class Gender extends ReadableEnum
{
    const UNKNOW = 'unknown';
    const MALE = 'male';
    const FEMALE = 'female';

    public static function getPossibleValues(): array
    {
        return [
            self::UNKNOW,
            self::MALE,
            self::FEMALE,
        ];
    }

    public static function getReadables(): array
    {
        return [
            self::UNKNOW => 'Unknown',
            self::MALE => 'Male',
            self::FEMALE => 'Female',
        ];
    }
}
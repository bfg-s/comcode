<?php

namespace Bfg\Comcode\Tests;


class TestedClass extends Model implements Arrayable, ArrayAccess
{
}
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Arrayable;
use ArrayAccess;
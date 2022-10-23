<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static YES()
 * @method static static NO()
 */
final class ActionEnum extends Enum
{
    const NO = 0;
    const YES = 1;
}

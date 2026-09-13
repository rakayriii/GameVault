<?php

namespace App\Enums;

enum MessageType: string
{
    case Text = 'text';
    case System = 'system';
    case Credential = 'credential';
    case Dispute = 'dispute';
}

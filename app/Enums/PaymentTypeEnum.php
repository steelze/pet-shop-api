<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case CREDIT_CARD = 'credit_card';

    case CASH_ON_DELIVERY = 'cash_on_delivery';

    case BANK_TRANSFER = 'bank_transfer';
}

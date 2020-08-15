<?php
/**
 * Created by PhpStorm.
 * User: nahuel
 * Date: 02/07/18
 * Time: 09:26
 */

namespace Combinatoria\OrderWorkflow\Model;


class Workflow
{
    //states
    const STATE_INVOICED = 'invoiced';
    const STATE_SHIPPING = 'shipping';
    const STATE_PAYMENT_ACCREDITED = 'payment_accredited';

    //status
    const STATUS_INVOICED_PENDING   = 'invoice_pending';
    const STATUS_INVOICED_INVOICED  = 'invoiced';
    const STATUS_SHIPPING_PENDING   = 'shipping_pending';
    const STATUS_SHIPPING_DELIVERED = 'delivered';
    const STATUS_SHIPPING_RECEIVED  = 'received';
    const STATUS_PAYMENT_ACCREDITED = 'payment_accredited';


}
<?php

namespace App\Services;

use App\Models\tracking_info_model;

class tracking_info_service
{
    public static function add(
        $ticketId,
        $userId,
        $typeOfTicket,
        $action,
    ) {

        tracking_info_model::create([
            'ticket_id' => $ticketId,
            'type_of_ticket' => $typeOfTicket,
            'user_id' => $userId,
            'action' => $action,
        ]);

    }
}
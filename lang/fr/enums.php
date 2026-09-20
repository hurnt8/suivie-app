<?php

return [

    'roles' => [
        'admin' => 'Administrateur',
        'operator' => 'Opérateur',
    ],

    'shipment_status' => [
        'pending' => 'En attente',
        'registered' => 'Colis enregistré',
        'picked_up' => 'Colis récupéré',
        'sorting_center' => 'Arrivé au centre de tri',
        'in_transit' => 'En transit',
        'customs' => 'Dédouanement',
        'destination_center' => 'Arrivé dans le pays de destination',
        'out_for_delivery' => 'En cours de livraison',
        'delivered' => 'Livré',
        'delayed' => 'Retardé',
        'returned' => 'Retourné',
        'cancelled' => 'Annulé',
    ],

    'shipment_status_description' => [
        'pending' => "L'expédition a été créée et est en attente de traitement.",
        'registered' => 'Le colis a été enregistré dans notre système.',
        'picked_up' => "Le colis a été récupéré auprès de l'expéditeur.",
        'sorting_center' => 'Le colis est arrivé au centre de tri.',
        'in_transit' => 'Votre colis est actuellement en transit vers le centre logistique de destination.',
        'customs' => 'Le colis est en cours de dédouanement.',
        'destination_center' => 'Le colis est arrivé dans le pays de destination.',
        'out_for_delivery' => "Le colis est en cours de livraison à l'adresse du destinataire.",
        'delivered' => 'Le colis a été livré avec succès.',
        'delayed' => 'La livraison du colis a pris du retard.',
        'returned' => "Le colis a été retourné à l'expéditeur.",
        'cancelled' => "L'expédition a été annulée.",
    ],

    'service_type' => [
        'standard' => 'Standard',
        'express' => 'Express',
        'economy' => 'Économique',
        'international' => 'International',
    ],

    'shipment_type' => [
        'document' => 'Document',
        'parcel' => 'Colis',
        'pallet' => 'Palette',
        'fragile' => 'Fragile',
    ],

    'notification_status' => [
        'pending' => 'En attente',
        'sent' => 'Envoyée',
        'failed' => 'Échec',
    ],

    'notification_channel' => [
        'mail' => 'E-mail',
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
    ],

];

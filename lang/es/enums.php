<?php

return [

    'roles' => [
        'admin' => 'Administrador',
        'operator' => 'Operador',
    ],

    'shipment_status' => [
        'pending' => 'Pendiente',
        'registered' => 'Envío registrado',
        'picked_up' => 'Recogido',
        'sorting_center' => 'Llegada al centro de clasificación',
        'in_transit' => 'En tránsito',
        'customs' => 'Despacho de aduana',
        'destination_center' => 'Llegada al país de destino',
        'out_for_delivery' => 'En reparto',
        'delivered' => 'Entregado',
        'delayed' => 'Retrasado',
        'returned' => 'Devuelto',
        'cancelled' => 'Cancelado',
    ],

    'shipment_status_description' => [
        'pending' => 'El envío ha sido creado y está a la espera de ser procesado.',
        'registered' => 'El paquete ha sido registrado en nuestro sistema.',
        'picked_up' => 'El paquete ha sido recogido en la dirección del remitente.',
        'sorting_center' => 'El paquete ha llegado al centro de clasificación.',
        'in_transit' => 'Su paquete está actualmente en tránsito hacia el centro logístico de destino.',
        'customs' => 'El paquete está en proceso de despacho de aduana.',
        'destination_center' => 'El paquete ha llegado al país de destino.',
        'out_for_delivery' => 'El paquete está en reparto hacia la dirección del destinatario.',
        'delivered' => 'El paquete ha sido entregado con éxito.',
        'delayed' => 'La entrega del paquete se ha retrasado.',
        'returned' => 'El paquete ha sido devuelto al remitente.',
        'cancelled' => 'El envío ha sido cancelado.',
    ],

    'service_type' => [
        'standard' => 'Estándar',
        'express' => 'Exprés',
        'economy' => 'Económico',
        'international' => 'Internacional',
    ],

    'shipment_type' => [
        'document' => 'Documento',
        'parcel' => 'Paquete',
        'pallet' => 'Palé',
        'fragile' => 'Frágil',
    ],

    'notification_status' => [
        'pending' => 'Pendiente',
        'sent' => 'Enviada',
        'failed' => 'Fallida',
    ],

    'notification_channel' => [
        'mail' => 'Correo electrónico',
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
    ],

];

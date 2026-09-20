<?php

return [

    'roles' => [
        'admin' => 'Administrador',
        'operator' => 'Operador',
    ],

    'shipment_status' => [
        'pending' => 'Pendente',
        'registered' => 'Encomenda registada',
        'picked_up' => 'Recolhida',
        'sorting_center' => 'Chegada ao centro de triagem',
        'in_transit' => 'Em trânsito',
        'customs' => 'Desalfandegamento',
        'destination_center' => 'Chegada ao país de destino',
        'out_for_delivery' => 'Em distribuição',
        'delivered' => 'Entregue',
        'delayed' => 'Atrasada',
        'returned' => 'Devolvida',
        'cancelled' => 'Cancelada',
    ],

    'shipment_status_description' => [
        'pending' => 'A expedição foi criada e aguarda processamento.',
        'registered' => 'A encomenda foi registada no nosso sistema.',
        'picked_up' => 'A encomenda foi recolhida junto do remetente.',
        'sorting_center' => 'A encomenda chegou ao centro de triagem.',
        'in_transit' => 'A sua encomenda está atualmente em trânsito para o centro logístico de destino.',
        'customs' => 'A encomenda está em processo de desalfandegamento.',
        'destination_center' => 'A encomenda chegou ao país de destino.',
        'out_for_delivery' => 'A encomenda está a ser entregue ao destinatário.',
        'delivered' => 'A encomenda foi entregue com sucesso.',
        'delayed' => 'A entrega da encomenda sofreu um atraso.',
        'returned' => 'A encomenda foi devolvida ao remetente.',
        'cancelled' => 'A expedição foi cancelada.',
    ],

    'service_type' => [
        'standard' => 'Standard',
        'express' => 'Expresso',
        'economy' => 'Económico',
        'international' => 'Internacional',
    ],

    'shipment_type' => [
        'document' => 'Documento',
        'parcel' => 'Encomenda',
        'pallet' => 'Palete',
        'fragile' => 'Frágil',
    ],

    'notification_status' => [
        'pending' => 'Pendente',
        'sent' => 'Enviada',
        'failed' => 'Falhada',
    ],

    'notification_channel' => [
        'mail' => 'E-mail',
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
    ],

];

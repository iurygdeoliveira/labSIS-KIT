<div>
    <style>
        /* Ajuste do badge da notificação para ficar mais perto do sininho e longe do botão a direita */
        .fi-topbar-database-notifications-btn .fi-icon-btn-badge-ctn {
            inset-inline-start: 15% !important; /* Posiciona no lado esquerdo */
            inset-inline-end: auto !important;
            top: 2px !important; /* Ajuste para cima/baixo */
            transform: translate(-1%, 10%) !important;
        }
        
        .fi-topbar-database-notifications-btn {
            margin-right: 0.5rem;
        }
    </style>
    @include('filament-panels::components.topbar.database-notifications-trigger')
</div>

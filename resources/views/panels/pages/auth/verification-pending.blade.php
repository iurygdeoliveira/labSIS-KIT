@php
    use Filament\Support\Facades\FilamentView;
    use Filament\View\PanelsRenderHook;
@endphp

<x-filament-panels::page.simple>
    {{ FilamentView::renderHook(PanelsRenderHook::AUTH_REGISTER_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <div class="text-center space-y-4">
        <div class="mx-auto w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h2 class="text-xl font-semibold text-gray-900">
            Sua conta está aguardando aprovação
        </h2>
        
        <p class="text-gray-600 max-w-md mx-auto">
            Administradores revisarão sua solicitação e entrarão em contato em breve. 
            Você receberá um email quando sua conta for aprovada.
        </p>
        
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 max-w-md mx-auto">
            <p class="text-sm text-amber-800">
                <strong>Dica:</strong> Verifique sua caixa de entrada e spam para atualizações sobre o status da sua conta.
            </p>
        </div>
    </div>

    {{ FilamentView::renderHook(PanelsRenderHook::AUTH_REGISTER_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
</x-filament-panels::page.simple>

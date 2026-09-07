@php
    use Filament\Support\Facades\FilamentView;
    use Filament\View\PanelsRenderHook;
@endphp

<x-filament-panels::page.simple>
    {{ FilamentView::renderHook(PanelsRenderHook::AUTH_REGISTER_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <div class="text-center space-y-4">
        <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>
        
        <h2 class="text-xl font-semibold text-gray-900">
            Sua conta foi suspensa
        </h2>
        
        <p class="text-gray-600 max-w-md mx-auto">
            Sua conta foi temporariamente suspensa devido a violações dos termos de uso. 
            Entre em contato com o suporte para mais informações sobre como reativar sua conta.
        </p>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-w-md mx-auto">
            <p class="text-sm text-red-800">
                <strong>Importante:</strong> Se você acredita que isso é um erro, entre em contato com nossa equipe de suporte o mais rápido possível.
            </p>
        </div>

        <div class="pt-4">
            <a href="mailto:suporte@exemplo.com" 
               class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Entrar em contato com o suporte
            </a>
        </div>
    </div>

    {{ FilamentView::renderHook(PanelsRenderHook::AUTH_REGISTER_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
</x-filament-panels::page.simple>

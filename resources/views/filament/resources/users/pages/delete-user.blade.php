<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Alerta de Aviso --}}
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="h-5 w-5 text-red-400 text-center font-bold">⚠</div>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                        Atenção: Exclusão Permanente
                    </h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        <p>
                            Você está prestes a excluir permanentemente este usuário. 
                            Esta ação <strong>não pode ser desfeita</strong> e todos os dados 
                            associados ao usuário serão removidos completamente do sistema.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informações do Usuário --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Informações do Usuário
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome: {{ $this->getRecord()->name ?? 'Sem nome' }}</label>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email: {{ $this->getRecord()->email ?? 'Sem email' }}</label>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status: {{ $this->getRecord()->is_suspended ? 'Suspenso' : 'Ativo' }}</label>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Criado em: {{ $this->getRecord()->created_at?->format('d/m/Y H:i') ?? 'Data não disponível' }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

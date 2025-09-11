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
                            Esta ação <strong>não pode ser desfeita</strong> e o arquivo será 
                            removido completamente do sistema.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informações do Arquivo --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Informações do Arquivo
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome do Arquivo: {{ $this->getRecord()->getFirstMedia('media')?->name ?? 'Sem nome' }}</label>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo: {{ $this->getRecord()->file_type ?? 'Desconhecido' }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
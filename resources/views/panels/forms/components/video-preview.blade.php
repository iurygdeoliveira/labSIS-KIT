<!--
    Componente de preview de vídeo do Filament.
    - Inicializa o estado `url` com o valor atual de `video.url` vindo do servidor ($get).
    - Calcula a URL de incorporação (`embedUrl`) no front-end para YouTube e Vimeo.
    - Atualiza `url` ao ouvir o evento global `video-toggled`, evitando roundtrips ao servidor.
-->
<div
    x-data="{
        url: @js($get('video.url')),
        get isYoutube() { return this.url && (this.url.includes('youtube.com/watch') || this.url.includes('youtu.be/')); },
        get isVimeo() { return this.url && this.url.includes('vimeo.com/'); },
        get embedUrl() {
            if (!this.url) { return null; }
            // YouTube
            if (this.isYoutube) {
                try {
                    if (this.url.includes('youtube.com/watch')) {
                        const u = new URL(this.url);
                        const id = u.searchParams.get('v');
                        return id ? `https://www.youtube.com/embed/${id}` : null;
                    }
                    if (this.url.includes('youtu.be/')) {
                        const u = new URL(this.url);
                        const path = u.pathname.split('/').filter(Boolean);
                        const id = path[0] ?? null;
                        return id ? `https://www.youtube.com/embed/${id}` : null;
                    }
                } catch (_) { return null; }
            }
            // Vimeo
            if (this.isVimeo) {
                try {
                    const u = new URL(this.url);
                    const segments = u.pathname.split('/').filter(Boolean);
                    const id = segments.pop();
                    return id ? `https://player.vimeo.com/video/${id}` : null;
                } catch (_) { return null; }
            }
            return null;
        }
    }"
    x-on:video-toggled.window="url = $event.detail.url ?? null"
>
    <!-- Renderiza o iframe quando a URL é válida e reconhecida (YouTube/Vimeo) -->
    <template x-if="embedUrl">
        <div class="mt-4">
            <div class="aspect-video w-full">
                <iframe
                    x-bind:src="embedUrl"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    class="w-full h-full rounded-lg shadow-md">
                </iframe>
            </div>
        </div>
    </template>

    <!-- Exibe aviso quando há URL mas ela não é suportada para preview -->
    <template x-if="!embedUrl && url">
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
                <strong>URL não suportada:</strong> Apenas URLs do YouTube e Vimeo são suportadas para preview.
            </p>
            <p class="text-xs text-yellow-600 mt-1" x-text="`URL inserida: ${url}`"></p>
        </div>
    </template>
</div>

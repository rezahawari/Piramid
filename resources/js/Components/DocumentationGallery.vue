<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    // Array of { id, stage, type: 'photo'|'video', file_url, caption }
    documentations: { type: Array, default: () => [] },
});

const grouped = computed(() => {
    const stageOrder = ['menunggu', 'dibayar', 'hewan_disiapkan', 'tersembelih', 'didistribusikan'];
    const groups = {};
    for (const doc of props.documentations) {
        (groups[doc.stage] ??= []).push(doc);
    }
    return stageOrder.filter((s) => groups[s]).map((s) => ({ stage: s, items: groups[s] }));
});

const activeModalDoc = ref(null);
</script>

<template>
    <div v-if="grouped.length" class="space-y-6">
        <div v-for="group in grouped" :key="group.stage" class="rounded-2xl border border-gray-100 bg-gray-50/50 p-4">
            <div class="mb-3 flex items-center justify-between">
                <StatusBadge :status="group.stage" />
                <span class="text-xs text-gray-400 font-semibold">{{ group.items.length }} File</span>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                <figure
                    v-for="doc in group.items"
                    :key="doc.id"
                    class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xs cursor-pointer transition hover:-translate-y-0.5 hover:shadow-md"
                    @click="activeModalDoc = doc"
                >
                    <div class="relative h-32 w-full overflow-hidden bg-black/90 flex items-center justify-center">
                        <video
                            v-if="doc.type === 'video'"
                            :src="doc.file_url"
                            class="h-full w-full object-cover pointer-events-none"
                            preload="metadata"
                        />
                        <img
                            v-else
                            :src="doc.file_url"
                            :alt="doc.caption ?? 'Dokumentasi'"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                            loading="lazy"
                        />

                        <!-- Video Play Icon Overlay -->
                        <div v-if="doc.type === 'video'" class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/40 transition">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500 text-white shadow-md">
                                <svg class="h-5 w-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <figcaption class="p-2 text-xs">
                        <p class="font-bold text-gray-800 truncate">{{ doc.caption || (doc.type === 'video' ? 'Video Pelaksanaan' : 'Foto Pelaksanaan') }}</p>
                        <span class="text-[10px] text-brand-600 font-semibold">Klik untuk buka &rarr;</span>
                    </figcaption>
                </figure>
            </div>
        </div>

        <!-- Lightbox / Modal Player -->
        <div
            v-if="activeModalDoc"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4 backdrop-blur-sm"
            @click.self="activeModalDoc = null"
        >
            <div class="relative w-full max-w-4xl overflow-hidden rounded-2xl bg-zinc-900 shadow-2xl">
                <button
                    type="button"
                    class="absolute right-4 top-4 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-black/60 text-white hover:bg-black"
                    @click="activeModalDoc = null"
                >
                    &times;
                </button>

                <div class="relative aspect-video w-full bg-black flex items-center justify-center">
                    <video
                        v-if="activeModalDoc.type === 'video'"
                        :src="activeModalDoc.file_url"
                        class="h-full w-full"
                        controls
                        autoplay
                    />
                    <img
                        v-else
                        :src="activeModalDoc.file_url"
                        :alt="activeModalDoc.caption ?? 'Dokumentasi'"
                        class="h-full w-full object-contain"
                    />
                </div>

                <div class="bg-white p-4 flex items-center justify-between">
                    <div>
                        <span class="rounded bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-700 uppercase">
                            Tahap: {{ activeModalDoc.stage }}
                        </span>
                        <p class="mt-1 text-sm font-bold text-gray-900">{{ activeModalDoc.caption || 'Dokumentasi Pelaksanaan Ibadah' }}</p>
                    </div>
                    <a
                        :href="activeModalDoc.file_url"
                        target="_blank"
                        rel="noopener"
                        class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-100"
                    >
                        Buka Tab Baru &nearr;
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="rounded-2xl border border-dashed border-gray-200 p-8 text-center text-xs text-gray-400">
        Belum ada berkas dokumentasi foto atau video yang diunggah untuk pesanan ini.
    </div>
</template>

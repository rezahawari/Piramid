<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import { computed } from 'vue';

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
</script>

<template>
    <div v-if="grouped.length" class="space-y-6">
        <div v-for="group in grouped" :key="group.stage">
            <div class="mb-2">
                <StatusBadge :status="group.stage" />
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                <figure v-for="doc in group.items" :key="doc.id" class="overflow-hidden rounded-lg border bg-white">
                    <a :href="doc.file_url" target="_blank" rel="noopener">
                        <video
                            v-if="doc.type === 'video'"
                            :src="doc.file_url"
                            class="h-32 w-full object-cover"
                            controls
                            preload="metadata"
                        />
                        <img
                            v-else
                            :src="doc.file_url"
                            :alt="doc.caption ?? 'Dokumentasi'"
                            class="h-32 w-full object-cover"
                            loading="lazy"
                        />
                    </a>
                    <figcaption v-if="doc.caption" class="truncate px-2 py-1 text-xs text-gray-500">
                        {{ doc.caption }}
                    </figcaption>
                </figure>
            </div>
        </div>
    </div>
    <p v-else class="text-sm text-gray-500">Belum ada dokumentasi.</p>
</template>

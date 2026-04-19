<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

type Props = {
    team: Team;
};

const props = defineProps<Props>();

defineOptions({
    layout: (props: Props) => [
        AppLayout,
        {
            breadcrumbs: [
                {
                    title: 'Dashboard',
                    href: dashboard(),
                },
                {
                    title: props.team.name,
                },
            ],
        },
    ],
});
</script>

<template>
    <Head :title="team.name" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <h1 class="text-2xl font-semibold">{{ team.name }}</h1>
        <p class="text-sm text-muted-foreground">Your role: {{ team.role }}</p>
    </div>
</template>

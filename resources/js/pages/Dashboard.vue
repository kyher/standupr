<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ShowTeamController from '@/actions/App/Http/Controllers/Teams/ShowTeamController';
import CreateTeam from '@/components/CreateTeam.vue';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

type Props = {
    teams: Team[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <div>
            <CreateTeam />
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Link
                v-for="team in teams"
                :key="team.id"
                :href="ShowTeamController.url(team)"
            >
                <Card class="gap-2 transition-shadow hover:shadow-md">
                    <CardHeader>
                        <CardTitle>{{ team.name }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            Your role: <span class="capitalize">{{ team.role }}</span>
                        </p>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </div>
</template>

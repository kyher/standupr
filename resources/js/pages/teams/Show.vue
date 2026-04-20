<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import DestroyTeamController from '@/actions/App/Http/Controllers/Teams/DestroyTeamController';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

defineProps<{
    team: Team;
}>();

defineOptions({
    layout: (props: { team: Team }) => [
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
        <div v-if="team.role === 'admin'">
            <Form
                v-bind="DestroyTeamController.form.delete(team)"
                v-slot="{ processing }"
            >
                <Button
                    type="submit"
                    variant="destructive"
                    :disabled="processing"
                >
                    Delete team
                </Button>
            </Form>
        </div>
    </div>
</template>

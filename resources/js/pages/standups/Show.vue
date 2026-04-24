<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import StoreStandupNoteController from '@/actions/App/Http/Controllers/Standups/StoreStandupNoteController';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { Standup, StandupNote, Team } from '@/types';
import ShowTeamController from '@/actions/App/Http/Controllers/Teams/ShowTeamController';

const props = defineProps<{
    team: { id: string; name: string };
    standup: Standup;
    notes: StandupNote[];
}>();

defineOptions({
    layout: (props: { team: Team; standup: Standup }) => [
        AppLayout,
        {
            breadcrumbs: [
                {
                    title: 'Dashboard',
                    href: dashboard(),
                },
                {
                    title: props.team.name,
                    href: ShowTeamController.url(props.team),
                },
                {
                    title: `Stand-up – ${props.standup.date}`,
                },
            ],
        },
    ],
});

const form = useForm({ body: '' });

function submit() {
    form.post(StoreStandupNoteController.url([props.team, props.standup]), {
        onSuccess: () => form.reset(),
    });
}

const grouped = computed(() => {
    const map = new Map<number, { name: string; notes: StandupNote[] }>();
    for (const note of props.notes) {
        if (!map.has(note.user.id)) {
            map.set(note.user.id, { name: note.user.name, notes: [] });
        }
        map.get(note.user.id)!.notes.push(note);
    }
    return [...map.values()];
});
</script>

<template>
    <Head :title="`Stand-up – ${standup.date}`" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <div>
            <h1 class="text-2xl font-semibold">Stand-up</h1>
            <p class="text-sm text-muted-foreground">{{ standup.date }}</p>
        </div>

        <div class="flex flex-col gap-6">
            <div
                v-if="grouped.length === 0"
                class="text-sm text-muted-foreground"
            >
                No notes yet. Be the first to add one!
            </div>

            <div
                v-for="member in grouped"
                :key="member.name"
                class="flex flex-col gap-2"
            >
                <h2 class="font-semibold">{{ member.name }}</h2>
                <ul class="flex flex-col gap-1">
                    <li
                        v-for="note in member.notes"
                        :key="note.id"
                        class="rounded-md border p-3 text-sm"
                    >
                        {{ note.body }}
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t pt-4">
            <h2 class="mb-3 font-semibold">Add your update</h2>
            <form class="flex flex-col gap-3" @submit.prevent="submit">
                <textarea
                    v-model="form.body"
                    placeholder="What are you working on?"
                    rows="3"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                />
                <p v-if="form.errors.body" class="text-sm text-destructive">
                    {{ form.errors.body }}
                </p>
                <div>
                    <Button type="submit" :disabled="form.processing">
                        Add note
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Trash2Icon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import DestroyStandupNoteController from '@/actions/App/Http/Controllers/Standups/DestroyStandupNoteController';
import StoreStandupNoteController from '@/actions/App/Http/Controllers/Standups/StoreStandupNoteController';
import ShowTeamController from '@/actions/App/Http/Controllers/Teams/ShowTeamController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useBlockerTag } from '@/composables/useBlockerTag';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { Standup, StandupNote, Team } from '@/types';

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

const page = usePage();
const currentUserId = computed(() => page.props.auth.user.id);

const form = useForm({ body: '', has_blocker: false });

const textarea = ref<HTMLTextAreaElement | null>(null);
const body = computed({
    get: () => form.body,
    set: (v) => { form.body = v; },
});

const { hasBlocker, showHint: showBlockerHint, updateHint, handleTab } = useBlockerTag(body, textarea);

function submit() {
    form.has_blocker = hasBlocker.value;
    form.post(StoreStandupNoteController.url([props.team, props.standup]), {
        onSuccess: () => {
            form.reset();
            showBlockerHint.value = false;
        },
    });
}

const filterBlockers = ref(false);

const grouped = computed(() => {
    const map = new Map<number, { name: string; notes: StandupNote[] }>();

    for (const note of props.notes) {
        if (filterBlockers.value && !note.has_blocker) continue;

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
            <div class="flex items-center gap-2">
                <Button
                    type="button"
                    :variant="filterBlockers ? 'destructive' : 'outline'"
                    size="sm"
                    @click="filterBlockers = !filterBlockers"
                >
                    Blockers only
                </Button>
            </div>

            <div
                v-if="grouped.length === 0"
                class="text-sm text-muted-foreground"
            >
                {{
                    filterBlockers
                        ? 'No blockers today.'
                        : 'No notes yet. Be the first to add one!'
                }}
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
                        class="flex items-start justify-between gap-2 rounded-md border p-3 text-sm"
                        :class="
                            note.has_blocker
                                ? 'border-destructive/50 bg-destructive/5'
                                : ''
                        "
                    >
                        <div class="flex flex-col gap-1.5">
                            <span>{{ note.body }}</span>
                            <Badge
                                v-if="note.has_blocker"
                                variant="destructive"
                            >
                                Blocker
                            </Badge>
                        </div>
                        <button
                            v-if="note.user.id === currentUserId"
                            type="button"
                            class="shrink-0 cursor-pointer text-muted-foreground hover:text-destructive"
                            @click="
                                router.delete(
                                    DestroyStandupNoteController.url([
                                        team,
                                        standup,
                                        note,
                                    ]),
                                )
                            "
                        >
                            <Trash2Icon class="size-4" />
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t pt-4">
            <h2 class="mb-3 font-semibold">Add your update</h2>
            <form class="flex flex-col gap-3" @submit.prevent="submit">
                <textarea
                    ref="textarea"
                    v-model="form.body"
                    placeholder="What are you working on?"
                    rows="3"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                    @input="updateHint"
                    @click="updateHint"
                    @keyup="updateHint"
                    @keydown.tab="handleTab"
                />
                <p v-if="form.errors.body" class="text-sm text-destructive">
                    {{ form.errors.body }}
                </p>
                <Badge
                    v-if="hasBlocker"
                    variant="destructive"
                    class="self-start"
                >
                    Blocker identified
                </Badge>
                <p class="text-sm text-muted-foreground">
                    Type #blocker anywhere in your note to flag it as a blocker
                    <span v-if="showBlockerHint" class="ml-1 text-foreground"
                        >— press
                        <kbd class="rounded border px-1 font-mono text-xs"
                            >Tab</kbd
                        >
                        to complete</span
                    >
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

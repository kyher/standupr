<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import StoreStandupController from '@/actions/App/Http/Controllers/Standups/StoreStandupController';
import ShowStandupController from '@/actions/App/Http/Controllers/Standups/ShowStandupController';
import DestroyTeamController from '@/actions/App/Http/Controllers/Teams/DestroyTeamController';
import StoreTeamInvitationController from '@/actions/App/Http/Controllers/Teams/Invitations/StoreTeamInvitationController';
import DestroyTeamInvitationController from '@/actions/App/Http/Controllers/Teams/Invitations/DestroyTeamInvitationController';
import EditTeamModal from '@/components/EditTeamModal.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { Standup, Team, TeamInvitation, TeamMember } from '@/types';

defineProps<{
    team: Team;
    today_standup: Standup | null;
    previous_standups: Standup[];
    members: TeamMember[];
    pending_invitations: TeamInvitation[] | null;
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

        <div v-if="team.role === 'admin'" class="flex gap-2">
            <EditTeamModal :team="team">
                <Button variant="outline">Edit team</Button>
            </EditTeamModal>
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

        <div class="mt-4 border-t pt-4">
            <h2 class="mb-3 text-lg font-semibold">Today's stand-up</h2>

            <div v-if="today_standup">
                <Button as-child>
                    <Link
                        :href="ShowStandupController.url([team, today_standup])"
                    >
                        View stand-up
                    </Link>
                </Button>
            </div>

            <div v-else class="flex flex-col gap-3">
                <p class="text-sm text-muted-foreground">
                    No stand-up has been created for today yet.
                </p>
                <Form
                    v-bind="StoreStandupController.form.post(team)"
                    v-slot="{ processing }"
                >
                    <Button type="submit" :disabled="processing">
                        Create stand-up
                    </Button>
                </Form>
            </div>
        </div>

        <div v-if="previous_standups.length > 0" class="border-t pt-4">
            <h2 class="mb-3 text-lg font-semibold">Previous stand-ups</h2>
            <ul class="flex flex-col gap-1">
                <li v-for="standup in previous_standups" :key="standup.id">
                    <Link
                        :href="ShowStandupController.url([team, standup])"
                        class="text-sm hover:underline"
                    >
                        {{ standup.date }}
                    </Link>
                </li>
            </ul>
        </div>

        <div class="border-t pt-4">
            <h2 class="mb-3 text-lg font-semibold">Members</h2>
            <ul class="flex flex-col gap-1">
                <li
                    v-for="member in members"
                    :key="member.id"
                    class="flex items-center justify-between text-sm"
                >
                    <span>{{ member.name }}</span>
                    <span class="text-muted-foreground">{{ member.role }}</span>
                </li>
            </ul>
        </div>

        <div v-if="team.role === 'admin'" class="border-t pt-4">
            <h2 class="mb-3 text-lg font-semibold">Invite member</h2>
            <Form
                v-bind="StoreTeamInvitationController.form.post(team)"
                :options="{ preserveScroll: true }"
                class="flex gap-2"
                v-slot="{ errors, processing }"
                reset-on-success
            >
                <div class="flex flex-1 flex-col gap-1">
                    <Label for="email" class="sr-only">Email address</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        placeholder="Email address"
                        required
                    />
                    <InputError :message="errors.email" />
                </div>
                <Button type="submit" :disabled="processing">
                    Send invite
                </Button>
            </Form>

            <div
                v-if="pending_invitations && pending_invitations.length > 0"
                class="mt-4"
            >
                <h3 class="mb-2 text-sm font-medium">Pending invitations</h3>
                <ul class="flex flex-col gap-2">
                    <li
                        v-for="invitation in pending_invitations"
                        :key="invitation.id"
                        class="flex items-center justify-between text-sm"
                    >
                        <div>
                            <span>{{ invitation.user.name }}</span>
                            <span class="ml-1 text-muted-foreground">
                                ({{ invitation.user.email }})
                            </span>
                        </div>
                        <Form
                            v-bind="DestroyTeamInvitationController.form.delete({ team, invitation })"
                            v-slot="{ processing }"
                        >
                            <Button
                                type="submit"
                                variant="ghost"
                                size="sm"
                                :disabled="processing"
                            >
                                Cancel
                            </Button>
                        </Form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

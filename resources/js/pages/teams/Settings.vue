<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import DestroyTeamController from '@/actions/App/Http/Controllers/Teams/DestroyTeamController';
import DestroyTeamInvitationController from '@/actions/App/Http/Controllers/Teams/Invitations/DestroyTeamInvitationController';
import StoreTeamInvitationController from '@/actions/App/Http/Controllers/Teams/Invitations/StoreTeamInvitationController';
import ShowTeamController from '@/actions/App/Http/Controllers/Teams/ShowTeamController';
import UpdateTeamController from '@/actions/App/Http/Controllers/Teams/UpdateTeamController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { Team, TeamInvitation, TeamMember } from '@/types';

defineProps<{
    team: Team;
    pending_invitations: TeamInvitation[];
    members: TeamMember[];
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
                    href: ShowTeamController.url(props.team),
                },
                {
                    title: 'Settings',
                },
            ],
        },
    ],
});
</script>

<template>
    <Head :title="`${team.name} — Settings`" />

    <h1 class="sr-only">Team settings</h1>

    <div class="flex flex-col space-y-6">
        <div class="my-4 flex flex-col space-y-4">
            <Heading
                variant="small"
                title="Team name"
                description="Update your team's display name"
            />
            <Form
                :action="UpdateTeamController.url(team)"
                method="patch"
                :options="{ preserveScroll: true }"
                class="flex gap-2"
                v-slot="{ errors, processing }"
            >
                <div class="flex flex-1 flex-col gap-1">
                    <Label for="name" class="sr-only">Team name</Label>
                    <Input
                        id="name"
                        name="name"
                        :default-value="team.name"
                        placeholder="Team name"
                        required
                    />
                    <InputError :message="errors.name" />
                </div>
                <Button type="submit" :disabled="processing">Save</Button>
            </Form>
        </div>

        <div class="flex flex-col space-y-4 border-t pt-6">
            <Heading variant="small" title="Members" />
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

        <div class="flex flex-col space-y-4 border-t pt-6">
            <Heading
                variant="small"
                title="Invite member"
                description="Invite a new member to the team by email"
            />
            <Form
                :action="StoreTeamInvitationController.url(team)"
                method="post"
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
                <Button type="submit" :disabled="processing"
                    >Send invite</Button
                >
            </Form>

            <div v-if="pending_invitations.length > 0">
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
                            :action="
                                DestroyTeamInvitationController.url({
                                    team,
                                    invitation,
                                })
                            "
                            method="delete"
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

        <div class="flex flex-col space-y-4 border-t pt-6">
            <Heading
                variant="small"
                title="Delete team"
                description="Permanently delete this team and all its data"
            />
            <div
                class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10"
            >
                <div
                    class="relative space-y-0.5 text-red-600 dark:text-red-100"
                >
                    <p class="font-medium">Warning</p>
                    <p class="text-sm">
                        Please proceed with caution, this cannot be undone.
                    </p>
                </div>
                <Form
                    :action="DestroyTeamController.url(team)"
                    method="delete"
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
    </div>
</template>

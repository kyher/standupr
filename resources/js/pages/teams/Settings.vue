<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import DestroyTeamController from '@/actions/App/Http/Controllers/Teams/DestroyTeamController';
import DestroyTeamInvitationController from '@/actions/App/Http/Controllers/Teams/Invitations/DestroyTeamInvitationController';
import ShowTeamController from '@/actions/App/Http/Controllers/Teams/ShowTeamController';
import Heading from '@/components/Heading.vue';
import InviteTeamMember from '@/components/InviteTeamMember.vue';
import UpdateTeamName from '@/components/UpdateTeamName.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { Team, TeamInvitation, TeamMember } from '@/types';

const confirmTeamName = ref('');

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
                title="Update team"
                description="Manage your team's name and membership"
            />
            <div class="flex gap-2">
                <UpdateTeamName :team="team" />
                <InviteTeamMember :team="team" />
            </div>
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
                    <span class="capitalize text-muted-foreground">{{ member.role }}</span>
                </li>
            </ul>

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
                <Dialog
                    @update:open="(open) => !open && (confirmTeamName = '')"
                >
                    <DialogTrigger as-child>
                        <Button variant="destructive">Delete team</Button>
                    </DialogTrigger>
                    <DialogContent>
                        <Form
                            :action="DestroyTeamController.url(team)"
                            method="delete"
                            v-slot="{ processing }"
                            class="space-y-6"
                        >
                            <DialogHeader class="space-y-3">
                                <DialogTitle
                                    >Are you sure you want to delete this
                                    team?</DialogTitle
                                >
                                <DialogDescription>
                                    This action cannot be undone. All team data
                                    will be permanently deleted. Please type
                                    <strong>{{ team.name }}</strong> to confirm.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="grid gap-2">
                                <Label for="confirm-team-name"
                                    >Team name</Label
                                >
                                <Input
                                    id="confirm-team-name"
                                    v-model="confirmTeamName"
                                    placeholder="Enter team name"
                                    autocomplete="off"
                                />
                            </div>

                            <DialogFooter class="gap-2">
                                <DialogClose as-child>
                                    <Button
                                        type="button"
                                        variant="secondary"
                                        @click="confirmTeamName = ''"
                                    >
                                        Cancel
                                    </Button>
                                </DialogClose>
                                <Button
                                    type="submit"
                                    variant="destructive"
                                    :disabled="
                                        processing ||
                                        confirmTeamName !== team.name
                                    "
                                >
                                    Delete team
                                </Button>
                            </DialogFooter>
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    </div>
</template>

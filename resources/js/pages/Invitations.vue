<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import AcceptInvitationController from '@/actions/App/Http/Controllers/Invitations/AcceptInvitationController';
import RejectInvitationController from '@/actions/App/Http/Controllers/Invitations/RejectInvitationController';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { PendingInvitation } from '@/types';

defineProps<{
    invitations: PendingInvitation[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Invitations',
            },
        ],
    },
});
</script>

<template>
    <Head title="Invitations" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <h1 class="text-2xl font-semibold">Invitations</h1>

        <div v-if="invitations.length === 0">
            <p class="text-sm text-muted-foreground">
                You have no pending invitations.
            </p>
        </div>

        <ul v-else class="flex flex-col gap-3">
            <li
                v-for="invitation in invitations"
                :key="invitation.id"
                class="flex items-center justify-between rounded-lg border p-4"
            >
                <div>
                    <p class="font-medium">{{ invitation.team.name }}</p>
                    <p class="text-sm text-muted-foreground">
                        Invited by {{ invitation.invited_by.name }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Form
                        v-bind="AcceptInvitationController.form.post(invitation)"
                        v-slot="{ processing }"
                    >
                        <Button type="submit" :disabled="processing">
                            Accept
                        </Button>
                    </Form>
                    <Form
                        v-bind="RejectInvitationController.form.post(invitation)"
                        v-slot="{ processing }"
                    >
                        <Button
                            type="submit"
                            variant="outline"
                            :disabled="processing"
                        >
                            Decline
                        </Button>
                    </Form>
                </div>
            </li>
        </ul>
    </div>
</template>

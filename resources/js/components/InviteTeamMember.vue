<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import StoreTeamInvitationController from '@/actions/App/Http/Controllers/Teams/Invitations/StoreTeamInvitationController';
import InputError from '@/components/InputError.vue';
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
import type { Team } from '@/types';

defineProps<{ team: Team }>();

const open = ref(false);
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button variant="secondary">Invite member</Button>
        </DialogTrigger>
        <DialogContent>
            <Form
                :action="StoreTeamInvitationController.url(team)"
                method="post"
                class="space-y-6"
                v-slot="{ errors, processing }"
                reset-on-success
                @success="open = false"
            >
                <DialogHeader class="space-y-3">
                    <DialogTitle>Invite member</DialogTitle>
                    <DialogDescription>
                        Invite a new member to the team by email.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        placeholder="Email address"
                    />
                    <InputError :message="errors.email" />
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary">
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">
                        Send invite
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>

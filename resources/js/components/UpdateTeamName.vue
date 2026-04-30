<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import UpdateTeamController from '@/actions/App/Http/Controllers/Teams/UpdateTeamController';
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
            <Button variant="secondary">Rename team</Button>
        </DialogTrigger>
        <DialogContent>
            <Form
                :action="UpdateTeamController.url(team)"
                method="patch"
                class="space-y-6"
                v-slot="{ errors, processing }"
                @success="open = false"
            >
                <DialogHeader class="space-y-3">
                    <DialogTitle>Rename team</DialogTitle>
                    <DialogDescription>
                        Update your team's display name.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="name">Team name</Label>
                    <Input
                        id="name"
                        name="name"
                        :default-value="team.name"
                        placeholder="Team name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary">
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">Save</Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>

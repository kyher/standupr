<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import StoreTeamController from '@/actions/App/Http/Controllers/Teams/StoreTeamController';
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

const open = ref(false);
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button>Create team</Button>
        </DialogTrigger>
        <DialogContent>
            <Form
                :action="StoreTeamController.url()"
                method="post"
                class="space-y-6"
                v-slot="{ errors, processing }"
                reset-on-success
                @success="open = false"
            >
                <DialogHeader class="space-y-3">
                    <DialogTitle>Create team</DialogTitle>
                    <DialogDescription>
                        Enter a name for your new team.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="name">Team name</Label>
                    <Input id="name" name="name" placeholder="Team name" />
                    <InputError :message="errors.name" />
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary">
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing">
                        Create team
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>

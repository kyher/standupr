<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ShowTeamController from '@/actions/App/Http/Controllers/Teams/ShowTeamController';
import StoreTeamController from '@/actions/App/Http/Controllers/Teams/StoreTeamController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

type Props = {
    teams: Team[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <Form
            :action="StoreTeamController.url()"
            method="post"
            class="space-y-4"
            v-slot="{ errors, processing }"
            reset-on-success
        >
            <div class="grid gap-2">
                <Label for="name">Team name</Label>
                <Input id="name" name="name" placeholder="Team name" required />
                <InputError :message="errors.name" />
            </div>

            <Button :disabled="processing">Create team</Button>
        </Form>

        <div class="grid gap-4 md:grid-cols-3">
            <Link
                v-for="team in teams"
                :key="team.id"
                :href="ShowTeamController.url(team)"
            >
                <Card class="gap-2 transition-shadow hover:shadow-md">
                    <CardHeader>
                        <CardTitle>{{ team.name }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            Your role: {{ team.role }}
                        </p>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </div>
</template>

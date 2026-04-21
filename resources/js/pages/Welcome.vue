<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { buttonVariants } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Welcome" />
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-background p-6 text-foreground"
    >
        <header class="absolute top-0 right-0 p-6">
            <nav class="flex items-center gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    :class="buttonVariants({ variant: 'outline' })"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        :class="buttonVariants({ variant: 'ghost' })"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        :class="buttonVariants()"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <main class="flex flex-col items-center gap-4 text-center">
            <h1 class="text-6xl font-bold tracking-tight">Standupr</h1>
            <p class="text-xl text-muted-foreground">
                Stand up updates without the effort.
            </p>
        </main>
    </div>
</template>

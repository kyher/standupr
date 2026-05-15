import { computed, nextTick, ref  } from 'vue';
import type {Ref} from 'vue';

const BLOCKER = '#blocker';
const BLOCKER_RE = /(?<!\S)#blocker/i;
const WORD_AT_CURSOR_RE = /(?<!\S)#[a-z]*$/i;

export function useBlockerTag(body: Ref<string>, textarea: Ref<HTMLTextAreaElement | null>) {
    const showHint = ref(false);

    const hasBlocker = computed(() => BLOCKER_RE.test(body.value));

    function getActiveToken(): { start: number; end: number } | null {
        if (!textarea.value) {
return null;
}

        const cursor = textarea.value.selectionStart ?? body.value.length;
        const before = body.value.slice(0, cursor);
        const match = before.match(WORD_AT_CURSOR_RE);

        if (!match) {
return null;
}

        const word = match[0].toLowerCase();

        if (!BLOCKER.startsWith(word) || word === BLOCKER || word.length < 3) {
return null;
}

        return { start: before.length - match[0].length, end: cursor };
    }

    function updateHint() {
        showHint.value = getActiveToken() !== null;
    }

    function handleTab(e: KeyboardEvent) {
        const token = getActiveToken();

        if (!token) {
return;
}

        e.preventDefault();
        body.value = body.value.slice(0, token.start) + BLOCKER + body.value.slice(token.end);
        nextTick(() => {
            const pos = token.start + '#blocker'.length;
            textarea.value?.setSelectionRange(pos, pos);
        });
        showHint.value = false;
    }

    return { hasBlocker, showHint, updateHint, handleTab };
}

import { describe, expect, it, vi } from 'vitest';
import { nextTick, ref } from 'vue';
import { useBlockerTag } from './useBlockerTag';

function makeTextarea(text: string, cursor: number) {
    return ref({
        selectionStart: cursor,
        setSelectionRange: vi.fn(),
    } as unknown as HTMLTextAreaElement);
}

function setup(text: string, cursor?: number) {
    const body = ref(text);
    const textarea = makeTextarea(text, cursor ?? text.length);
    const composable = useBlockerTag(body, textarea);

    return { body, textarea, ...composable };
}

describe('hasBlocker', () => {
    it('is false for empty body', () => {
        const { hasBlocker } = setup('');
        expect(hasBlocker.value).toBe(false);
    });

    it('is true for #blocker', () => {
        const { hasBlocker } = setup('#blocker');
        expect(hasBlocker.value).toBe(true);
    });

    it('is case insensitive', () => {
        expect(setup('#BLOCKER').hasBlocker.value).toBe(true);
        expect(setup('#Blocker').hasBlocker.value).toBe(true);
        expect(setup('#bLoCkEr').hasBlocker.value).toBe(true);
    });

    it('is true when #blocker appears mid-sentence', () => {
        const { hasBlocker } = setup('stuck on auth #blocker need help');
        expect(hasBlocker.value).toBe(true);
    });

    it('is true when #blocker follows a newline', () => {
        const { hasBlocker } = setup('working on auth\n#blocker');
        expect(hasBlocker.value).toBe(true);
    });

    it('is false when # is preceded by a non-whitespace character', () => {
        const { hasBlocker } = setup('abc#blocker');
        expect(hasBlocker.value).toBe(false);
    });

    it('is false when there is a space between # and blocker', () => {
        const { hasBlocker } = setup('# blocker');
        expect(hasBlocker.value).toBe(false);
    });
});

describe('showHint / updateHint', () => {
    it('shows hint for #bl at cursor', () => {
        const { showHint, updateHint } = setup('#bl');
        updateHint();
        expect(showHint.value).toBe(true);
    });

    it('shows hint for longer valid prefixes', () => {
        for (const prefix of ['#bl', '#blo', '#bloc', '#block', '#blocke']) {
            const { showHint, updateHint } = setup(prefix);
            updateHint();
            expect(showHint.value).toBe(true);
        }
    });

    it('does not show hint for #b (too short)', () => {
        const { showHint, updateHint } = setup('#b');
        updateHint();
        expect(showHint.value).toBe(false);
    });

    it('does not show hint when #blocker is already complete', () => {
        const { showHint, updateHint } = setup('#blocker');
        updateHint();
        expect(showHint.value).toBe(false);
    });

    it('does not show hint for non-prefix like #blz', () => {
        const { showHint, updateHint } = setup('#blz');
        updateHint();
        expect(showHint.value).toBe(false);
    });

    it('does not show hint for unrelated words like #bless', () => {
        const { showHint, updateHint } = setup('#bless');
        updateHint();
        expect(showHint.value).toBe(false);
    });

    it('does not show hint when # is preceded by a non-whitespace character', () => {
        const { showHint, updateHint } = setup('abc#bl');
        updateHint();
        expect(showHint.value).toBe(false);
    });

    it('shows hint when partial tag follows a space', () => {
        const { showHint, updateHint } = setup('working on auth #bl');
        updateHint();
        expect(showHint.value).toBe(true);
    });

    it('shows hint based on cursor position, not end of string', () => {
        const body = ref('#bl world');
        const textarea = ref({
            selectionStart: 3,
            setSelectionRange: vi.fn(),
        } as unknown as HTMLTextAreaElement);
        const { showHint, updateHint } = useBlockerTag(body, textarea);
        updateHint();
        expect(showHint.value).toBe(true);
    });

    it('does not show hint when cursor is past the partial tag', () => {
        const body = ref('#bl world');
        const textarea = ref({
            selectionStart: 9,
            setSelectionRange: vi.fn(),
        } as unknown as HTMLTextAreaElement);
        const { showHint, updateHint } = useBlockerTag(body, textarea);
        updateHint();
        expect(showHint.value).toBe(false);
    });
});

describe('handleTab', () => {
    it('completes #bl to #blocker', async () => {
        const { body, handleTab } = setup('#bl');
        const event = { preventDefault: vi.fn() } as unknown as KeyboardEvent;
        handleTab(event);
        expect(event.preventDefault).toHaveBeenCalled();
        expect(body.value).toBe('#blocker');
    });

    it('completes a partial prefix mid-sentence', async () => {
        const { body, handleTab } = setup('working on auth #blo');
        const event = { preventDefault: vi.fn() } as unknown as KeyboardEvent;
        handleTab(event);
        expect(body.value).toBe('working on auth #blocker');
    });

    it('preserves text after the cursor when completing', async () => {
        const body = ref('#bl world');
        const textarea = ref({
            selectionStart: 3,
            setSelectionRange: vi.fn(),
        } as unknown as HTMLTextAreaElement);
        const { handleTab } = useBlockerTag(body, textarea);
        const event = { preventDefault: vi.fn() } as unknown as KeyboardEvent;
        handleTab(event);
        expect(body.value).toBe('#blocker world');
    });

    it('moves cursor to end of completed word', async () => {
        const { textarea, handleTab } = setup('working on auth #blo');
        const event = { preventDefault: vi.fn() } as unknown as KeyboardEvent;
        handleTab(event);
        await nextTick();
        expect(textarea.value?.setSelectionRange).toHaveBeenCalledWith(24, 24);
    });

    it('hides the hint after completing', async () => {
        const { showHint, updateHint, handleTab } = setup('#blo');
        updateHint();
        expect(showHint.value).toBe(true);
        handleTab({ preventDefault: vi.fn() } as unknown as KeyboardEvent);
        expect(showHint.value).toBe(false);
    });

    it('does nothing when there is no active token', () => {
        const { body, handleTab } = setup('no tag here');
        const event = { preventDefault: vi.fn() } as unknown as KeyboardEvent;
        handleTab(event);
        expect(event.preventDefault).not.toHaveBeenCalled();
        expect(body.value).toBe('no tag here');
    });
});

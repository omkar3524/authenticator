import { ref } from 'vue';

const toasts = ref<Array<{
    id: string;
    title?: string;
    description?: string;
    variant?: 'default' | 'destructive' | 'success';
    duration?: number;
}>>([]);

export function useToast() {
    const toast = ({ title, description, variant = 'default', duration = 3000 }: {
        title?: string;
        description?: string;
        variant?: 'default' | 'destructive' | 'success';
        duration?: number;
    }) => {
        const id = Math.random().toString(36).substring(2, 9);
        const newToast = { id, title, description, variant, duration };

        toasts.value.push(newToast);

        if (duration !== Infinity) {
            setTimeout(() => {
                dismiss(id);
            }, duration);
        }

        return id;
    };

    const dismiss = (id: string) => {
        const index = toasts.value.findIndex((t) => t.id === id);
        if (index !== -1) {
            toasts.value.splice(index, 1);
        }
    };

    return {
        toasts,
        toast,
        dismiss,
    };
}

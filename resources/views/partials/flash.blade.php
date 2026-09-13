@if(session('status'))
    <div class="max-w-[1340px] mx-auto px-6 lg:px-8 relative z-10 {{ ($compact ?? false) ? '' : '-mt-0 pt-12' }}">
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-tertiary-container/15 border border-tertiary/20 text-on-surface">
            <span class="material-symbols-outlined text-tertiary">check_circle</span>
            <span class="font-body-sm text-body-sm">{{ session('status') }}</span>
            <button type="button" class="ml-auto text-on-surface-variant hover:text-on-surface" onclick="this.parentElement.remove()">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="max-w-[1340px] mx-auto px-6 lg:px-8 relative z-10 {{ ($compact ?? false) ? '' : '-mt-0 pt-12' }}">
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-error-container/15 border border-error/20 text-on-surface">
            <span class="material-symbols-outlined text-error">error</span>
            <span class="font-body-sm text-body-sm">{{ session('error') }}</span>
            <button type="button" class="ml-auto text-on-surface-variant hover:text-on-surface" onclick="this.parentElement.remove()">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    </div>
@endif
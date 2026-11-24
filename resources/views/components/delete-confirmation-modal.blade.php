<dialog id="delete-confirmation-modal" class="m-auto w-[min(420px,calc(100vw-2rem))] rounded-2xl border border-slate-200 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/50">
    <div class="p-6 sm:p-7">
        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-red-50 text-red-600"><i data-lucide="trash-2" class="h-5 w-5"></i></span>
        <h2 class="mt-4 text-lg font-bold">Hapus Data?</h2>
        <p class="mt-2 text-sm leading-6 text-slate-600">Apakah kamu yakin ingin menghapus <strong data-delete-name class="text-slate-800"></strong>? Tindakan ini mungkin tidak dapat dibatalkan.</p>
        <p data-delete-error hidden class="mt-3 rounded-lg bg-red-50 p-3 text-xs font-medium text-red-700"></p>
        <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <button type="button" data-delete-cancel class="min-h-11 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
            <button type="button" data-delete-confirm class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-4 text-sm font-bold text-white hover:bg-red-700"><i data-lucide="trash-2" class="h-4 w-4"></i>Ya, Hapus Data</button>
        </div>
    </div>
</dialog>

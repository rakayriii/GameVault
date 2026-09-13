<?php

namespace App\Http\Controllers;

use App\Enums\ListingStatus;
use App\Models\Game;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GameController extends Controller
{
    public function adminIndex(Request $request): View
    {
        $games = Game::query()
            ->withCount(['accounts' => fn ($q) => $q->where('status', ListingStatus::Approved->value)])
            ->when($request->string('q')->toString(), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.games.index', compact('games'));
    }

    public function create(): View
    {
        return view('admin.games.form', ['game' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $game = Game::query()->create([
            'name' => $request->string('name')->trim()->toString(),
            'slug' => $this->uniqueGameSlug($request->string('name')->trim()->toString()),
            'icon_color' => $request->string('icon_color')->toString() ?: '#d0bcff',
            'description' => $request->string('description')->trim()->toString() ?: null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order'),
            'icon_url' => $this->storeMedia($request->file('icon'), null, 'icon'),
            'banner_url' => $this->storeMedia($request->file('banner'), null, 'banner'),
        ]);

        $this->moveMediaIntoSlugFolder($game);

        return redirect()->route('admin.games')->with('status', "Game '{$game->name}' ditambahkan.");
    }

    public function edit(Game $game): View
    {
        return view('admin.games.form', ['game' => $game]);
    }

    public function update(Game $game, Request $request): RedirectResponse
    {
        $this->validated($request, $game);

        $newSlug = $this->uniqueGameSlug($request->string('name')->trim()->toString(), $game->id);

        $game->forceFill([
            'name' => $request->string('name')->trim()->toString(),
            'slug' => $newSlug,
            'icon_color' => $request->string('icon_color')->toString() ?: '#d0bcff',
            'description' => $request->string('description')->trim()->toString() ?: null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order'),
            'icon_url' => $this->resolveMedia($request, 'icon', $game->icon_url, 'remove_icon', $newSlug),
            'banner_url' => $this->resolveMedia($request, 'banner', $game->banner_url, 'remove_banner', $newSlug),
        ])->save();

        if ($newSlug !== $game->slug) {
            $this->moveMediaIntoSlugFolder($game);
        }

        return redirect()->route('admin.games')->with('status', "Game '{$game->name}' diperbarui.");
    }

    public function destroy(Game $game): RedirectResponse
    {
        if ($game->accounts()->exists()) {
            return back()->with('error', 'Game memiliki listing akun aktif. Pindahkan atau nonaktifkan listing terlebih dahulu.');
        }

        foreach (['icon_url', 'banner_url'] as $attribute) {
            if ($game->{$attribute}) {
                Storage::disk('public')->delete($game->{$attribute});
            }
        }

        $game->delete();

        return back()->with('status', "Game '{$game->name}' dihapus.");
    }

    public function toggle(Game $game): RedirectResponse
    {
        $game->update(['is_active' => ! $game->is_active]);

        return back()->with('status', $game->is_active
            ? "Game '{$game->name}' diaktifkan."
            : "Game '{$game->name}' dinonaktifkan.");
    }

    private function validated(Request $request, ?Game $game = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:60'],
            'icon_color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,svg', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'remove_icon' => ['nullable', 'boolean'],
            'remove_banner' => ['nullable', 'boolean'],
        ];

        if ($game) {
            $rules['slug'] = ['nullable', 'string', Rule::unique('games', 'slug')->ignore($game->id)];
        }

        return $request->validate($rules, [], [
            'name' => 'Nama game',
            'icon_color' => 'Warna ikon',
            'description' => 'Deskripsi',
            'is_active' => 'Status aktif',
            'sort_order' => 'Urutan tampil',
            'icon' => 'Logo / ikon',
            'banner' => 'Banner',
        ]);
    }

    private function resolveMedia(Request $request, string $field, ?string $previous, string $removeFlag, string $folder): ?string
    {
        $path = $this->storeMedia($request->file($field), $previous, $field, $folder);

        if ($request->boolean($removeFlag) && ! $request->file($field) && $path) {
            Storage::disk('public')->delete($path);

            return null;
        }

        return $path;
    }

    private function storeMedia(?UploadedFile $file, ?string $previous, string $label, ?string $folder = null): ?string
    {
        if (! $file) {
            return $previous;
        }

        if ($previous) {
            Storage::disk('public')->delete($previous);
        }

        $path = $file->storeAs(
            'games/'.($folder ?: 'temp'),
            Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: $label).'-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension(),
            'public',
        );

        return $path;
    }

    private function moveMediaIntoSlugFolder(Game $game): void
    {
        $folder = 'games/'.$game->slug;
        $moved = false;

        foreach (['icon_url', 'banner_url'] as $attribute) {
            $oldPath = $game->{$attribute};

            if (! $oldPath || str_starts_with($oldPath, $folder.'/')) {
                continue;
            }

            $filename = basename($oldPath);
            Storage::disk('public')->move($oldPath, $folder.'/'.$filename);
            $game->forceFill([$attribute => $folder.'/'.$filename])->save();
            $moved = true;
        }

        if ($moved) {
            Storage::disk('public')->deleteDirectory('games/temp');
        }
    }

    private function uniqueGameSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'game';
        $slug = $base;
        $i = 2;

        while (Game::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\Content\MarkdownRenderer;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MagazinController extends Controller
{
    /** @var array<string, string> */
    public const CATEGORIES = [
        'aktuality' => 'Aktuality',
        'reforma'   => 'Reforma',
        'investice' => 'Investice',
        'rady'      => 'Rady',
        'data'      => 'Data',
    ];

    public function __construct(
        private readonly MarkdownRenderer $markdown,
    ) {}

    public function index(Request $request): View
    {
        $category = $request->query('kategorie');
        $search   = trim((string) $request->query('q', ''));

        if ($category !== null && ! array_key_exists($category, self::CATEGORIES)) {
            abort(404);
        }

        $articles = Article::published()
            ->when($category, fn ($q) => $q->byCategory($category))
            ->when($search !== '', fn ($q) => $q->where(
                fn ($sub) => $sub->where('title', 'like', "%{$search}%")
                    ->orWhere('perex', 'like', "%{$search}%"),
            ))
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        $categoryCounts = Article::published()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $meta = [
            'title'       => 'Magazín o důchodech — Aktuality, průvodci a rady | Důchody.cz',
            'description' => 'Aktuální informace o důchodech, valorizaci a penzijním spoření. Průvodci pro každého, kdo plánuje odchod do důchodu.',
            'canonical'   => route('magazin.index'),
        ];

        return view('content.magazin.index', [
            'articles'       => $articles,
            'categories'     => self::CATEGORIES,
            'categoryCounts' => $categoryCounts,
            'activeCategory' => $category,
            'search'         => $search,
            'meta'           => $meta,
        ]);
    }

    public function show(string $slug): View
    {
        $article = Article::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $contentHtml = $this->markdown->render($article->content_markdown);

        $related = Article::published()
            ->byCategory($article->category)
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $meta = [
            'title'       => $article->seo_title ?? $article->title,
            'description' => $article->seo_description ?? mb_substr($article->perex, 0, 160),
            'canonical'   => route('magazin.show', $slug),
            'ogType'      => 'article',
        ];

        return view('content.magazin.show', [
            'article'       => $article,
            'contentHtml'   => $contentHtml,
            'related'       => $related,
            'categoryLabel' => self::CATEGORIES[$article->category] ?? $article->category,
            'meta'          => $meta,
        ]);
    }
}

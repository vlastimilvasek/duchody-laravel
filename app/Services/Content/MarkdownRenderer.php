<?php

declare(strict_types=1);

namespace App\Services\Content;

use Illuminate\Support\HtmlString;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Renderuje markdown článků (GFM) a zpracovává redakční shortcody:
 *
 *   [info]…[/info]       — modrý informační box
 *   [warning]…[/warning] — žlutý varovný box
 *   [kalkulacka]         — inline CTA na kalkulačku důchodu
 */
final class MarkdownRenderer
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        $environment = new Environment([
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    public function render(string $markdown): HtmlString
    {
        [$markdown, $placeholders] = $this->extractShortcodes($markdown);

        $html = $this->converter->convert($markdown)->getContent();

        // Placeholdery vyměnit až po konverzi — markdown je nesmí zpracovat.
        // Token skončí obalený v <p>; nahrazujeme včetně obalu.
        foreach ($placeholders as $token => $replacement) {
            $html = str_replace(["<p>{$token}</p>", $token], [$replacement, $replacement], $html);
        }

        return new HtmlString($html);
    }

    /**
     * Nahradí shortcody tokeny a připraví výsledné HTML bloky.
     *
     * @return array{0: string, 1: array<string, string>}
     */
    private function extractShortcodes(string $markdown): array
    {
        $placeholders = [];
        $counter      = 0;

        $replace = function (string $pattern, callable $htmlFactory) use (&$markdown, &$placeholders, &$counter): void {
            $markdown = (string) preg_replace_callback(
                $pattern,
                function (array $matches) use ($htmlFactory, &$placeholders, &$counter): string {
                    // Čistě textový token — přežije html_input=strip
                    $token                = "SHORTCODEPLACEHOLDER{$counter}X";
                    $placeholders[$token] = $htmlFactory($matches);
                    $counter++;

                    return "\n\n{$token}\n\n";
                },
                $markdown,
            );
        };

        $replace('/\[info\](.*?)\[\/info\]/s', fn (array $m): string => $this->infoBox(trim($m[1])));
        $replace('/\[warning\](.*?)\[\/warning\]/s', fn (array $m): string => $this->warningBox(trim($m[1])));
        $replace('/\[kalkulacka\]/', fn (): string => $this->calculatorCta());

        return [$markdown, $placeholders];
    }

    private function infoBox(string $content): string
    {
        $inner = $this->inline($content);

        return <<<HTML
            <div class="not-prose border-l-4 border-blue-300 bg-blue-50 rounded-r-xl px-5 py-4 my-6">
                <p class="text-sm text-blue-800 leading-relaxed">{$inner}</p>
            </div>
            HTML;
    }

    private function warningBox(string $content): string
    {
        $inner = $this->inline($content);

        return <<<HTML
            <div class="not-prose border-l-4 border-amber-400 bg-amber-50 rounded-r-xl px-5 py-4 my-6">
                <p class="text-sm text-amber-800 leading-relaxed">{$inner}</p>
            </div>
            HTML;
    }

    private function calculatorCta(): string
    {
        $url = route('kalkulacka.vyse');

        return <<<HTML
            <div class="not-prose bg-slate-900 rounded-2xl px-6 py-5 my-8 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <p class="text-white font-semibold">Kolik budete mít důchod vy?</p>
                    <p class="text-sm text-slate-400">Orientační výpočet podle parametrů 2026 — zdarma, do 2 minut.</p>
                </div>
                <a href="{$url}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors whitespace-nowrap shrink-0">
                    Spočítat důchod →
                </a>
            </div>
            HTML;
    }

    /** Obsah boxu projde markdownem (tučné písmo, odkazy) a zbaví se obalového <p>. */
    private function inline(string $content): string
    {
        $html = $this->converter->convert($content)->getContent();

        return trim((string) preg_replace('/^<p>|<\/p>$/', '', trim($html)));
    }
}

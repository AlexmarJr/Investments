<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    public function investments(Request $request)
    {
        // Multiple RSS/Atom feeds focused on economy/investments
        $feeds = [
            'https://www.infomoney.com.br/feed/',
            'https://exame.com/feed/',
            'https://g1.globo.com/economia/rss/',
        ];

        $items = Cache::remember('news_investments', 600, function () use ($feeds) {
            $aggregated = [];

            foreach ($feeds as $feedUrl) {
                try {
                    $resp = Http::withOptions(['verify' => false])->get($feedUrl);
                    $body = (string) $resp->body();
                    if (empty($body)) continue;

                    $xml = @simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
                    if (!$xml) continue;

                    if (isset($xml->channel->item)) {
                        foreach ($xml->channel->item as $node) {
                            $title = (string) ($node->title ?? '');
                            $link = (string) ($node->link ?? '');
                            $pubDate = (string) ($node->pubDate ?? '');
                            $pubTs = strtotime($pubDate) ?: null;
                            $description = (string) ($node->description ?? '');

                            $snippet = trim(strip_tags($description));
                            if (mb_strlen($snippet) > 200) $snippet = mb_substr($snippet, 0, 200) . '...';

                            $aggregated[] = [
                                'title' => $title,
                                'link' => $link,
                                'pubDate' => $pubDate,
                                'timestamp' => $pubTs,
                                'snippet' => $snippet,
                                'source' => parse_url($feedUrl, PHP_URL_HOST),
                            ];
                        }
                    } elseif (isset($xml->entry)) {
                        foreach ($xml->entry as $entry) {
                            $title = (string) ($entry->title ?? '');
                            $link = (string) ($entry->link['@attributes']['href'] ?? $entry->link ?? '');
                            $pubDate = (string) ($entry->updated ?? $entry->published ?? '');
                            $pubTs = strtotime($pubDate) ?: null;
                            $description = (string) ($entry->summary ?? $entry->content ?? '');

                            $snippet = trim(strip_tags($description));
                            if (mb_strlen($snippet) > 200) $snippet = mb_substr($snippet, 0, 200) . '...';

                            $aggregated[] = [
                                'title' => $title,
                                'link' => $link,
                                'pubDate' => $pubDate,
                                'timestamp' => $pubTs,
                                'snippet' => $snippet,
                                'source' => parse_url($feedUrl, PHP_URL_HOST),
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    // ignore individual feed failures
                    continue;
                }
            }

            // deduplicate by link or title
            $unique = [];
            foreach ($aggregated as $it) {
                $key = $it['link'] ?: md5($it['title']);
                if (isset($unique[$key])) continue;
                $unique[$key] = $it;
            }

            $list = array_values($unique);

            // sort by timestamp desc (items without timestamp go last)
            usort($list, function ($a, $b) {
                $ta = $a['timestamp'] ?? 0;
                $tb = $b['timestamp'] ?? 0;
                return $tb <=> $ta;
            });

            // return top 8 items and remove internal timestamp
            $list = array_slice($list, 0, 8);
            foreach ($list as &$l) {
                unset($l['timestamp']);
            }

            return $list;
        });

        return response()->json(['items' => $items]);
    }
}

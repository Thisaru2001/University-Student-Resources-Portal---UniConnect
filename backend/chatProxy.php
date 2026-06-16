<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$model     = $input['model']     ?? 'qwen3.5:4b';
$messages  = $input['messages']  ?? [];
$options   = $input['options']   ?? [];
$webSearch = $input['web_search'] ?? false;

if (empty($messages)) {
    echo json_encode(['error' => 'No messages received']);
    exit;
}

// ============================================================
// 1. WEB SEARCH (Wikipedia + DuckDuckGo — no API keys needed)
// ============================================================
if ($webSearch && !empty($messages)) {
    $lastMsg    = end($messages);
    $query      = $lastMsg['content'] ?? '';
    $searchResults = [];

    // Clean query — strip question words for better Wikipedia lookup
    $cleanQuery = preg_replace('/^(what is|what are|who is|who was|tell me about|explain|define|where is|when was|how is|how does|what was)\s+/i', '', $query);
    $cleanQuery = trim($cleanQuery);

    // --- Wikipedia: direct lookup first ---
    $wikiUrl = "https://en.wikipedia.org/api/rest_v1/page/summary/"
             . urlencode(str_replace(' ', '_', $cleanQuery));

    $ch = curl_init($wikiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_USERAGENT      => 'StudyBot/1.0',
    ]);
    $wikiResponse = curl_exec($ch);
    curl_close($ch);

    $wikiData = json_decode($wikiResponse, true);

    // If direct lookup failed or returned disambiguation, use Wikipedia Search API
    if (empty($wikiData['extract']) || ($wikiData['type'] ?? '') === 'disambiguation') {
        $searchUrl = "https://en.wikipedia.org/w/api.php?action=query&list=search&srsearch="
                   . urlencode($cleanQuery)
                   . "&format=json&srlimit=1";

        $ch = curl_init($searchUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_USERAGENT      => 'StudyBot/1.0',
        ]);
        $searchResponse = curl_exec($ch);
        curl_close($ch);

        $searchData  = json_decode($searchResponse, true);
        $firstTitle  = $searchData['query']['search'][0]['title'] ?? '';

        if ($firstTitle) {
            $wikiUrl2 = "https://en.wikipedia.org/api/rest_v1/page/summary/"
                      . urlencode(str_replace(' ', '_', $firstTitle));

            $ch = curl_init($wikiUrl2);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 8,
                CURLOPT_USERAGENT      => 'StudyBot/1.0',
            ]);
            $wikiResponse = curl_exec($ch);
            curl_close($ch);
            $wikiData = json_decode($wikiResponse, true);
        }
    }

    if (!empty($wikiData['extract']) && ($wikiData['type'] ?? '') !== 'disambiguation') {
        $searchResults[] = [
            'title'   => $wikiData['title'] ?? 'Wikipedia',
            'snippet' => $wikiData['extract'],
            'link'    => $wikiData['content_urls']['desktop']['page'] ?? '',
        ];
    }

    // --- DuckDuckGo Instant Answer API ---
    $ddgUrl = "https://api.duckduckgo.com/?q="
            . urlencode($query)
            . "&format=json&no_redirect=1&no_html=1&skip_disambig=1";

    $ch = curl_init($ddgUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_USERAGENT      => 'StudyBot/1.0',
    ]);
    $ddgResponse = curl_exec($ch);
    curl_close($ch);

    if ($ddgResponse !== false) {
        $ddgData = json_decode($ddgResponse, true);

        if (!empty($ddgData['Abstract'])) {
            $searchResults[] = [
                'title'   => $ddgData['Heading']     ?? 'DuckDuckGo',
                'snippet' => $ddgData['Abstract'],
                'link'    => $ddgData['AbstractURL'] ?? '',
            ];
        }

        foreach (($ddgData['RelatedTopics'] ?? []) as $topic) {
            if (count($searchResults) >= 3) break;
            if (!empty($topic['Text']) && !empty($topic['FirstURL'])) {
                $searchResults[] = [
                    'title'   => substr($topic['Text'], 0, 60),
                    'snippet' => $topic['Text'],
                    'link'    => $topic['FirstURL'],
                ];
            }
        }
    }

    // --- Build context for the model ---
    $context = "You have access to web search results. Use the following information to answer the user's question accurately.\n\n";

    if (!empty($searchResults)) {
        $context .= "Search results:\n";
        foreach ($searchResults as $idx => $result) {
            $context .= ($idx + 1) . ". " . $result['title'] . "\n";
            $context .= "   " . $result['snippet'] . "\n";
            $context .= "   Source: " . $result['link'] . "\n\n";
        }
        $context .= "Answer based on the above. If the results are not relevant, say so.\n";
    } else {
        $context .= "No results were found for this query.\n";
    }

    array_unshift($messages, [
        'role'    => 'system',
        'content' => $context,
    ]);
}

// ============================================================
// 2. FORCE THINKING OFF
// ============================================================
$options['think'] = false;
$options['num_predict'] = $options['num_predict'] ?? 300;

$payload = json_encode([
    'model'    => $model,
    'stream'   => false,
    'messages' => $messages,
    'options'  => $options,
    'think'    => false,
]);

// ============================================================
// 3. OLLAMA REQUEST
// ============================================================
$ollamaUrls = [
    'http://127.0.0.1:11434/api/chat',
    'http://localhost:11434/api/chat',
    'http://host.docker.internal:11434/api/chat',
];

$response  = false;
$curlError = '';

foreach ($ollamaUrls as $url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);
    $response  = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response !== false && $httpCode === 200) break;
    $response = false;
}

if ($response === false) {
    echo json_encode(['error' => 'Cannot reach Ollama', 'detail' => $curlError]);
    exit;
}

// ============================================================
// 4. CLEAN RESPONSE (remove thinking blocks)
// ============================================================
$data    = json_decode($response, true);
$content = $data['message']['content'] ?? '';

// Strip <think>...</think> blocks
$content = preg_replace('/<think>[\s\S]*?<\/think>/i', '', $content);

// Strip plain-text thinking dumps (Qwen3.5 specific)
$thinkingHeaders = [
    '/^Thinking Process:.*?(?=\n[A-Z]|\n\n[A-Za-z])/si',
    '/^\*\*Thinking Process\*\*.*?(?=\n\n[A-Za-z])/si',
    '/^\d+\.\s+\*\*Analyze the Request.*?(?=Hello|Hi |Sure|I |The |Yes|No)/si',
];
foreach ($thinkingHeaders as $pattern) {
    $cleaned = preg_replace($pattern, '', $content);
    if ($cleaned && trim($cleaned) !== '') {
        $content = $cleaned;
    }
}

$content = trim($content);

if ($content === '' && isset($data['message']['thinking'])) {
    $content = trim($data['message']['thinking']);
}

$data['message']['content'] = $content;
echo json_encode($data);
exit;
?>
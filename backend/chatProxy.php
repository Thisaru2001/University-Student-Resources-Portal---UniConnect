<?php
require_once 'logger.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}



header('Content-Type: application/json');
// ============================================================
// LOAD .ENV FILE
// ============================================================
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!empty($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}
// ============================================================

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
// 0. IDENTIFY WHICH MODEL TO USE (Groq OR Ollama)
// ============================================================
// If the model name starts with "openai/" or "llama", send it to Groq.
// Otherwise, send it to local Ollama.
$isGroqModel = str_starts_with($model, 'openai/') || str_starts_with($model, 'llama') || str_starts_with($model, 'meta-llama/');

// ============================================================
// 1. SYSTEM PROMPT
// ============================================================
$options['think'] = false;
$options['num_predict'] = $options['num_predict'] ?? 800;

$systemPrompt = "You are a helpful coding assistant. 
CRITICAL FORMATTING RULES:
1. Always use Markdown formatting.
2. For code snippets, ALWAYS use triple backticks with the language name (e.g., ```python, ```php, ```html) at the beginning and end.
3. Separate code blocks from your explanation text clearly.
4. Use bullet points and short paragraphs for readability.";

// WITH THIS (only add system prompt if no vision content exists):
$hasVisionContent = false;
foreach ($messages as $msg) {
    if (is_array($msg['content'])) {
        $hasVisionContent = true;
        break;
    }
}
if (!$hasVisionContent) {
    array_unshift($messages, [
        'role'    => 'system',
        'content' => $systemPrompt
    ]);
}

// ============================================================
// 2. ROUTE TO CORRECT API (Groq OR Ollama)
// ============================================================
if ($isGroqModel) {
    
    // ============================================================
    // --- ROUTE TO GROQ ---
    // ============================================================
    
    // PUT YOUR GROQ API KEY HERE (starts with gsk_...)
       $GROQ_API_KEY = getenv('GROQ_API_KEY'); 

    $apiUrl = "https://api.groq.com/openai/v1/chat/completions";

       // Ensure messages are an array (Groq requires array for image prompts)
    $formattedMessages = $messages; 
    if (!is_array($formattedMessages)) {
        $formattedMessages = [$formattedMessages];
    }

    $payload = [
        'model' => $model,
        'messages' => $formattedMessages,
        'temperature' => $options['temperature'] ?? 0.7,
        'max_tokens' => $options['num_predict'] ?? 800,
        'stream' => false,
    ];

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, 
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $GROQ_API_KEY
        ],
        CURLOPT_TIMEOUT => 60
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        echo json_encode(['error' => 'Groq API Error (HTTP ' . $httpCode . ')', 'detail' => $errorData['error']['message'] ?? 'Unknown error']);
        exit;
    }

    $data = json_decode($response, true);
    $content = $data['choices'][0]['message']['content'] ?? '';

} else {
    // ============================================================
    // --- ROUTE TO OLLAMA (Qwen) ---
    // ============================================================
    // If Web Search is on for Ollama, we keep your custom scraping logic here
    if ($webSearch && !empty($messages)) {
        $lastMsg    = end($messages);
        $query      = $lastMsg['content'] ?? '';
        $searchResults = [];

        // Clean query for Wikipedia
        $cleanQuery = preg_replace('/^(what is|what are|who is|who was|tell me about|explain|define|where is|when was|how is|how does|what was)\s+/i', '', $query);
        $cleanQuery = trim($cleanQuery);

        // --- Wikipedia lookup ---
        $wikiUrl = "https://en.wikipedia.org/api/rest_v1/page/summary/"
                 . urlencode(str_replace(' ', '_', $cleanQuery));

        $ch = curl_init($wikiUrl);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8, CURLOPT_USERAGENT => 'StudyBot/1.0']);
        $wikiResponse = curl_exec($ch);
        curl_close($ch);
        $wikiData = json_decode($wikiResponse, true);

        if (empty($wikiData['extract']) || ($wikiData['type'] ?? '') === 'disambiguation') {
            $searchUrl = "https://en.wikipedia.org/w/api.php?action=query&list=search&srsearch="
                       . urlencode($cleanQuery) . "&format=json&srlimit=1";
            $ch = curl_init($searchUrl);
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8, CURLOPT_USERAGENT => 'StudyBot/1.0']);
            $searchResponse = curl_exec($ch);
            curl_close($ch);
            $searchData  = json_decode($searchResponse, true);
            $firstTitle  = $searchData['query']['search'][0]['title'] ?? '';
            if ($firstTitle) {
                $wikiUrl2 = "https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode(str_replace(' ', '_', $firstTitle));
                $ch = curl_init($wikiUrl2);
                curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8, CURLOPT_USERAGENT => 'StudyBot/1.0']);
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

        // --- DuckDuckGo ---
        $ddgUrl = "https://api.duckduckgo.com/?q=" . urlencode($query) . "&format=json&no_redirect=1&no_html=1&skip_disambig=1";
        $ch = curl_init($ddgUrl);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8, CURLOPT_USERAGENT => 'StudyBot/1.0']);
        $ddgResponse = curl_exec($ch);
        curl_close($ch);

        if ($ddgResponse !== false) {
            $ddgData = json_decode($ddgResponse, true);
            if (!empty($ddgData['Abstract'])) {
                $searchResults[] = [
                    'title'   => $ddgData['Heading'] ?? 'DuckDuckGo',
                    'snippet' => $ddgData['Abstract'],
                    'link'    => $ddgData['AbstractURL'] ?? '',
                ];
            }
        }

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
        array_unshift($messages, ['role' => 'system', 'content' => $context]);
    }

    // Send to Ollama
    $payload = json_encode([
        'model'    => $model,
        'stream'   => false,
        'messages' => $messages,
        'options'  => $options,
        'think'    => false,
    ]);

    $ollamaUrls = ['http://127.0.0.1:11434/api/chat', 'http://localhost:11434/api/chat', 'http://host.docker.internal:11434/api/chat'];
    $response = false;
    foreach ($ollamaUrls as $url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload, CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 120
        ]);
        $response = curl_exec($ch);
        if ($response !== false && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200) break;
        curl_close($ch);
    }
    curl_close($ch);

    if ($response === false) {
        echo json_encode(['error' => 'Cannot reach Ollama']);
        exit;
    }

    $data = json_decode($response, true);
    $content = $data['message']['content'] ?? '';
}

// ============================================================
// 3. CLEAN RESPONSE
// ============================================================
$content = preg_replace('/<think>[\s\S]*?<\/think>/i', '', $content);
$content = trim($content);

echo json_encode(['message' => ['content' => $content]]);
exit;
?>
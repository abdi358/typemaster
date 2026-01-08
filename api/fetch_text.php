<?php
/**
 * TypeMaster - Fetch Text API
 * Returns random text for typing tests based on difficulty and mode
 */

require_once __DIR__ . '/../db/connect.php';

// Get parameters
$difficulty = sanitize($_GET['difficulty'] ?? 'easy');
$category = sanitize($_GET['category'] ?? 'common');
$wordCount = (int) ($_GET['count'] ?? 50);
$mode = sanitize($_GET['mode'] ?? 'words'); // words, sentences, paragraphs, code

// Validate inputs
$validDifficulties = ['easy', 'medium', 'hard'];
$validCategories = ['common', 'punctuation', 'numbers', 'code', 'quotes'];
$validModes = ['words', 'sentences', 'paragraphs', 'code'];

if (!in_array($difficulty, $validDifficulties)) {
    $difficulty = 'easy';
}

if (!in_array($mode, $validModes)) {
    $mode = 'words';
}

// Word count limits
$wordCount = max(10, min(500, $wordCount));

try {
    $text = generateText($difficulty, $mode, $wordCount);

    jsonResponse([
        'success' => true,
        'text' => $text,
        'difficulty' => $difficulty,
        'mode' => $mode,
        'wordCount' => str_word_count($text),
        'characterCount' => strlen($text)
    ]);

} catch (Exception $e) {
    error_log("Text generation error: " . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'Failed to generate text'], 500);
}

/**
 * Generate text based on mode and difficulty
 */
function generateText($difficulty, $mode, $wordCount)
{
    // Get text from database
    $texts = fetchAll(
        "SELECT content FROM text_sets WHERE difficulty = ? ORDER BY RAND() LIMIT 5",
        [$difficulty]
    );

    if (empty($texts)) {
        // Fallback to default words if no texts in database
        $texts = [['content' => getDefaultWords($difficulty)]];
    }

    // Combine texts and extract words
    $allWords = [];
    foreach ($texts as $text) {
        $words = preg_split('/\s+/', trim($text['content']));
        $allWords = array_merge($allWords, $words);
    }

    // Remove duplicates and shuffle
    $allWords = array_unique($allWords);
    shuffle($allWords);

    // Select required number of words
    $selectedWords = array_slice($allWords, 0, $wordCount);

    // If not enough words, repeat the list
    while (count($selectedWords) < $wordCount) {
        shuffle($allWords);
        $selectedWords = array_merge($selectedWords, $allWords);
        $selectedWords = array_slice($selectedWords, 0, $wordCount);
    }

    // Generate based on mode
    switch ($mode) {
        case 'sentences':
            return generateSentences($selectedWords, $difficulty);
        case 'paragraphs':
            return generateParagraphs($selectedWords, $difficulty);
        case 'code':
            return generateCodeText($wordCount);
        default:
            return implode(' ', $selectedWords);
    }
}

/**
 * Generate sentences from words
 */
function generateSentences($words, $difficulty)
{
    $sentences = [];
    $currentSentence = [];
    $sentenceLength = rand(5, 12);

    foreach ($words as $word) {
        $currentSentence[] = $word;

        if (count($currentSentence) >= $sentenceLength) {
            // Capitalize first word
            $currentSentence[0] = ucfirst($currentSentence[0]);

            // Create sentence
            $sentence = implode(' ', $currentSentence);

            // Add punctuation based on difficulty
            if ($difficulty !== 'easy') {
                $punctuation = ['.', '.', '.', '!', '?'];
                $sentence .= $punctuation[array_rand($punctuation)];
            } else {
                $sentence .= '.';
            }

            $sentences[] = $sentence;
            $currentSentence = [];
            $sentenceLength = rand(5, 12);
        }
    }

    // Add remaining words
    if (!empty($currentSentence)) {
        $currentSentence[0] = ucfirst($currentSentence[0]);
        $sentences[] = implode(' ', $currentSentence) . '.';
    }

    return implode(' ', $sentences);
}

/**
 * Generate paragraphs from words
 */
function generateParagraphs($words, $difficulty)
{
    $paragraphs = [];
    $currentParagraph = [];
    $paragraphLength = rand(3, 5); // sentences per paragraph
    $sentencesAdded = 0;
    $currentSentence = [];
    $sentenceLength = rand(5, 12);

    foreach ($words as $word) {
        $currentSentence[] = $word;

        if (count($currentSentence) >= $sentenceLength) {
            $currentSentence[0] = ucfirst($currentSentence[0]);
            $sentence = implode(' ', $currentSentence) . '.';
            $currentParagraph[] = $sentence;
            $sentencesAdded++;
            $currentSentence = [];
            $sentenceLength = rand(5, 12);

            if ($sentencesAdded >= $paragraphLength) {
                $paragraphs[] = implode(' ', $currentParagraph);
                $currentParagraph = [];
                $sentencesAdded = 0;
                $paragraphLength = rand(3, 5);
            }
        }
    }

    // Add remaining content
    if (!empty($currentSentence)) {
        $currentSentence[0] = ucfirst($currentSentence[0]);
        $currentParagraph[] = implode(' ', $currentSentence) . '.';
    }
    if (!empty($currentParagraph)) {
        $paragraphs[] = implode(' ', $currentParagraph);
    }

    return implode("\n\n", $paragraphs);
}

/**
 * Generate code snippets
 */
function generateCodeText($wordCount)
{
    $codeSnippets = [
        'const data = [];',
        'function processData(input) {',
        '  return input.filter(x => x > 0);',
        '}',
        'let count = 0;',
        'for (let i = 0; i < items.length; i++) {',
        '  count += items[i].value;',
        '}',
        'const result = await fetch(url);',
        'const json = await result.json();',
        'console.log("Success:", data);',
        'if (condition === true) {',
        '  handleSuccess();',
        '} else {',
        '  handleError();',
        '}',
        'class User {',
        '  constructor(name) {',
        '    this.name = name;',
        '  }',
        '}',
        'export default Component;',
        'import React from "react";',
        'const [state, setState] = useState(null);',
        'useEffect(() => {',
        '  loadData();',
        '}, []);',
        'try {',
        '  await saveData();',
        '} catch (error) {',
        '  console.error(error);',
        '}'
    ];

    // Get code from database if available
    $dbCode = fetchAll(
        "SELECT content FROM text_sets WHERE category = 'code' ORDER BY RAND() LIMIT 3"
    );

    if (!empty($dbCode)) {
        foreach ($dbCode as $row) {
            $codeSnippets[] = $row['content'];
        }
    }

    shuffle($codeSnippets);

    $result = [];
    $totalWords = 0;

    foreach ($codeSnippets as $snippet) {
        $result[] = $snippet;
        $totalWords += str_word_count($snippet);

        if ($totalWords >= $wordCount) {
            break;
        }
    }

    return implode(' ', $result);
}

/**
 * Get default words by difficulty
 */
function getDefaultWords($difficulty)
{
    switch ($difficulty) {
        case 'medium':
            return 'The quick brown fox jumps over the lazy dog. How vexingly quick daft zebras jump! Pack my box with five dozen liquor jugs. The five boxing wizards jump quickly. Sphinx of black quartz, judge my vow. Hello, world! How are you today? Programming is fun.';

        case 'hard':
            return 'function calculate() { return 42; } const API_URL = "https://example.com/api"; let total = price * quantity * 1.15; // Tax included. Email: user@domain.com | Phone: (555) 123-4567';

        case 'easy':
        default:
            return 'the be to of and a in that have I it for not on with he as you do at this but his by from they we say her she or an will my one all would there their what so up out if about who get which go me when make can like time no just him know take people into year your good some could them see other than then now look only come its over think also back after use two how our work first well way even new want because any these give day most us is very after also';
    }
}
